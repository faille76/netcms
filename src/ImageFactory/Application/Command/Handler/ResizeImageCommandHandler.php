<?php

declare(strict_types=1);

namespace App\ImageFactory\Application\Command\Handler;

use App\ImageFactory\Application\Command\ResizeImageCommand;
use App\ImageFactory\Domain\FilterTypeEnum;
use App\ImageFactory\Domain\Image;
use App\Shared\Application\Command\Handler\CommandHandlerInterface;
use ImagickException;
use Impulze\Bundle\InterventionImageBundle\ImageManager;
use Symfony\Component\Stopwatch\Stopwatch;

final class ResizeImageCommandHandler implements CommandHandlerInterface
{
    private ImageManager $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    public function __invoke(ResizeImageCommand $command): Image
    {
        $stopwatch = new Stopwatch(true);
        $stopwatch->start('resizeImageCommandHandler');

        $resizedImagePath = tempnam(
            sys_get_temp_dir(),
            FilterTypeEnum::getFilterName($command->getFilterType()) . $command->getFileName()
        );
        if (!is_string($resizedImagePath)) {
            throw new \RuntimeException('Cannot create temporary file.');
        }

        try {
            $image = $this->imageManager
                ->make($command->getPath() . '/' . $command->getFileName())
                ->resize(
                    FilterTypeEnum::getFilterWidth($command->getFilterType()),
                    FilterTypeEnum::getFilterHeight($command->getFilterType()),
                    function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    }
                )
                ->save($resizedImagePath, 100, $command->getFormat());
        } catch (ImagickException $e) {
            throw new \InvalidArgumentException('The file given is not an image.');
        }
        $stopwatch->stop('resizeImageCommandHandler');

        return new Image($resizedImagePath, $image->getWidth(), $image->getHeight());
    }
}
