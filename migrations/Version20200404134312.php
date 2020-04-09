<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200404134312 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Set up environment.';
    }

    public function up(Schema $schema) : void
    {
        // Articles
        $this->addSql('CREATE TABLE articles (
              id int(11) UNSIGNED NOT NULL,
              name varchar(100) NOT NULL,
              content text,
              created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              author_id int(11) UNSIGNED DEFAULT NULL,
              image varchar(100) DEFAULT NULL,
              view int(11) UNSIGNED NOT NULL DEFAULT \'0\',
              nb_comments int(11) UNSIGNED NOT NULL DEFAULT \'0\',
              slug varchar(100) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE articles
            ADD PRIMARY KEY (id),
            ADD UNIQUE KEY slug (slug) USING BTREE,
            ADD KEY article_author_id (author_id);');
        $this->addSql('ALTER TABLE articles MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;');

        // Comments
        $this->addSql('CREATE TABLE comments (
              id int(11) UNSIGNED NOT NULL,
              article_id int(11) UNSIGNED NOT NULL DEFAULT \'0\',
              content text NOT NULL,
              user_id int(11) UNSIGNED NOT NULL DEFAULT \'0\',
              created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              type tinyint(1) NOT NULL DEFAULT \'1\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE comments
            ADD PRIMARY KEY (id),
            ADD KEY comment_article_type (article_id,type),
            ADD KEY comment_user_id (user_id);');
        $this->addSql('ALTER TABLE comments MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;');

        // Configs
        $this->addSql('CREATE TABLE configs (
              id int(11) UNSIGNED NOT NULL,
              app_name varchar(50) DEFAULT NULL,
              app_full_name varchar(100) DEFAULT NULL,
              description varchar(255) DEFAULT NULL,
              tags varchar(255) DEFAULT NULL,
              email_address varchar(100) DEFAULT NULL,
              google_analytics varchar(20) DEFAULT NULL,
              recaptcha_client varchar(80) DEFAULT NULL,
              recaptcha_server varchar(80) DEFAULT NULL,
              facebook_page varchar(100) DEFAULT NULL,
              linkedin_page varchar(100) DEFAULT NULL,
              twitter_page varchar(100) DEFAULT NULL,
              phone_number varchar(20) DEFAULT NULL,
              postal_address varchar(150) DEFAULT NULL,
              features text
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE configs ADD PRIMARY KEY (id);');
        $this->addSql('ALTER TABLE configs MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;');

        // Contacts
        $this->addSql('CREATE TABLE contacts (
              id int(11) UNSIGNED NOT NULL,
              last_name varchar(50) NOT NULL,
              first_name varchar(50) NOT NULL,
              email varchar(100) DEFAULT NULL,
              role varchar(50) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE contacts ADD PRIMARY KEY (id);');
        $this->addSql('ALTER TABLE contacts MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;');

        // Page
        $this->addSql('CREATE TABLE pages (
              id int(11) UNSIGNED NOT NULL,
              slug varchar(100) NOT NULL,
              parent_id int(11) UNSIGNED DEFAULT NULL,
              enabled tinyint(1) NOT NULL DEFAULT \'1\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE pages
            ADD PRIMARY KEY (id),
            ADD UNIQUE KEY page_name (slug),
            ADD KEY page_parent_id (parent_id);');
        $this->addSql('ALTER TABLE pages MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;');

        // Page img
        $this->addSql('CREATE TABLE pages_img (
              id int(11) UNSIGNED NOT NULL,
              page_id int(11) UNSIGNED NOT NULL DEFAULT \'0\',
              name varchar(50) DEFAULT NULL,
              img varchar(50) DEFAULT NULL,
              size varchar(15) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE pages_img
            ADD PRIMARY KEY (id),
            ADD KEY page_id (page_id);');
        $this->addSql('ALTER TABLE pages_img MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;');

        // Page lang
        $this->addSql('CREATE TABLE pages_lang (
              page_id int(11) UNSIGNED NOT NULL DEFAULT \'0\',
              lang varchar(3) NOT NULL DEFAULT \'fr\',
              name varchar(100) NOT NULL,
              content text
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE pages_lang ADD UNIQUE KEY lang_idx (page_id,lang) USING BTREE;');

        // Partners
        $this->addSql('CREATE TABLE partners (
              id int(11) UNSIGNED NOT NULL,
              name varchar(50) NOT NULL,
              image varchar(50) NOT NULL,
              url varchar(120) NOT NULL,
              enabled tinyint(1) NOT NULL DEFAULT \'1\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE partners ADD PRIMARY KEY (id);');
        $this->addSql('ALTER TABLE partners MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;');

        // Photos album
        $this->addSql('CREATE TABLE photos_albums (
              id int(11) UNSIGNED NOT NULL,
              user_id int(11) UNSIGNED DEFAULT NULL,
              created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              name varchar(100) NOT NULL,
              relative_path varchar(255) NOT NULL,
              view int(11) UNSIGNED NOT NULL DEFAULT \'0\',
              nb_comments int(11) UNSIGNED NOT NULL DEFAULT \'0\',
              nb_pictures int(11) UNSIGNED NOT NULL DEFAULT \'0\',
              category_id int(11) UNSIGNED DEFAULT NULL,
              slug varchar(100) NOT NULL,
              ref_picture int(11) UNSIGNED DEFAULT NULL,
              enabled tinyint(1) NOT NULL DEFAULT \'1\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE photos_albums
            ADD PRIMARY KEY (id),
            ADD UNIQUE KEY name_page (slug),
            ADD KEY album_user_id (user_id),
            ADD KEY album_category_id (category_id),
            ADD KEY picture_ref (ref_picture);');
        $this->addSql('ALTER TABLE photos_albums MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;');

        // Photos cat
        $this->addSql('CREATE TABLE photos_cat (
              id int(11) UNSIGNED NOT NULL,
              name varchar(100) NOT NULL,
              parent_id int(11) UNSIGNED DEFAULT NULL,
              enabled tinyint(1) NOT NULL DEFAULT \'1\',
              slug varchar(100) NOT NULL,
              relative_path varchar(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE photos_cat
            ADD PRIMARY KEY (id),
            ADD UNIQUE KEY name_page (slug),
            ADD KEY cat_parent_id (parent_id);');
        $this->addSql('ALTER TABLE photos_cat MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;');

        // Photos img
        $this->addSql('CREATE TABLE photos_img (
              id int(11) UNSIGNED NOT NULL,
              album_id int(11) UNSIGNED NOT NULL DEFAULT \'0\',
              name varchar(100) DEFAULT NULL,
              size varchar(15) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE photos_img
            ADD PRIMARY KEY (id),
            ADD KEY img_album_id (album_id);');
        $this->addSql('ALTER TABLE photos_img MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;');

        $this->addSql('CREATE TABLE sessions (
              token varchar(100) NOT NULL,
              user_id int(11) UNSIGNED NOT NULL,
              created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              ip varchar(100) NOT NULL,
              user_agent varchar(255) DEFAULT NULL
            ) ENGINE=MEMORY DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE sessions ADD PRIMARY KEY (token);');

        // Upload
        $this->addSql('CREATE TABLE upload (
              id int(11) UNSIGNED NOT NULL,
              name varchar(100) NOT NULL,
              file_name varchar(100) NOT NULL,
              user_id int(11) UNSIGNED DEFAULT NULL,
              created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              type varchar(10) DEFAULT NULL,
              enabled tinyint(1) NOT NULL DEFAULT \'1\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE upload
            ADD PRIMARY KEY (id),
            ADD KEY upload_user_id (user_id);');
        $this->addSql('ALTER TABLE upload MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;');

        // Users
        $this->addSql('CREATE TABLE users (
              id int(11) UNSIGNED NOT NULL,
              username varchar(50) NOT NULL,
              password varchar(128) DEFAULT NULL,
              last_name varchar(50) NOT NULL,
              first_name varchar(50) NOT NULL,
              email varchar(100) NOT NULL,
              created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              birthday date NOT NULL,
              gender enum(\'h\',\'f\') NOT NULL DEFAULT \'h\',
              avatar varchar(100) DEFAULT NULL,
              newsletter tinyint(1) NOT NULL DEFAULT \'1\',
              rights text DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE users
            ADD PRIMARY KEY (id),
            ADD UNIQUE KEY pseudo (username),
            ADD UNIQUE KEY email (email);');
        $this->addSql('ALTER TABLE users MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;');

        // View
        $this->addSql('CREATE TABLE `view` (
              id int(11) UNSIGNED NOT NULL,
              created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              ip varchar(255) DEFAULT NULL,
              article_id int(11) UNSIGNED NOT NULL DEFAULT \'0\',
              type tinyint(1) NOT NULL DEFAULT \'1\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE `view`
            ADD PRIMARY KEY (id),
            ADD UNIQUE KEY unique_view_by_ip (article_id,type,ip);');
        $this->addSql('ALTER TABLE `view` MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;');

        // View user
        $this->addSql('CREATE TABLE view_users (
              id int(11) UNSIGNED NOT NULL,
              user_id int(11) UNSIGNED NOT NULL DEFAULT \'0\',
              created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              article_id int(11) UNSIGNED NOT NULL DEFAULT \'0\',
              type tinyint(1) NOT NULL DEFAULT \'1\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        $this->addSql('ALTER TABLE view_users
            ADD PRIMARY KEY (id),
            ADD UNIQUE KEY unique_view_by_user (article_id,type,user_id),
            ADD KEY view_user_id (user_id);');
        $this->addSql('ALTER TABLE view_users MODIFY id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;');

        $this->addSql(<<<SQL
            INSERT INTO `configs` (`id`, `app_name`, `app_full_name`, `description`, `tags`, `email_address`, `google_analytics`, `recaptcha_client`, `recaptcha_server`, `facebook_page`, `linkedin_page`, `twitter_page`, `phone_number`, `postal_address`, `features`) VALUES
            (1, 'NetCMS', 'NetCMS Simple to use, simple to develop', 'This website has been developed by Frederic Oddou.', 'symfony, doctrine, mysql, ddd, hexagonal, messenger, cqrs, eda, docker', 'frederic1.oddou@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{\"GALLERY\":true,\"PARTNER\":true,\"DOCUMENT\":true,\"CONTACT\":true,\"SEARCH\":true,\"ARTICLE\":true,\"COMMENT_ARTICLE\":true,\"COMMENT_ALBUM\":true,\"CAPTCHA\":false,\"ANALYTICS\":false,\"REGISTER\":true,\"LOST_PASSWORD\":true,\"PROFILE_UPDATE\":true,\"PROFILE_DETAILS\":true}');
        SQL);

        $this->addSql(<<<SQL
            INSERT INTO `users` (`id`, `username`, `password`, `last_name`, `first_name`, `email`, `created_at`, `updated_at`, `birthday`, `gender`, `avatar`, `newsletter`, `rights`) VALUES
            (1, 'admin', 'c3284d0f94606de1fd2af172aba15bf3', 'admin', 'admin', 'admin@admin.fr', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '2020-01-01', 'h', NULL, 1, '{\"admin\":true,\"configuration\":true,\"users\":true,\"pages\":true,\"articles\":true,\"pictures\":true,\"partners\":true,\"contact\":true,\"upload\":true,\"comments\":true,\"rights\":true}');
        SQL);

        // FOREIGN KEY
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT article_author_id FOREIGN KEY (author_id) REFERENCES `users` (id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT comment_user_id FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE NO ACTION ON UPDATE CASCADE;');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT page_parent_id FOREIGN KEY (parent_id) REFERENCES `pages` (id) ON DELETE NO ACTION ON UPDATE CASCADE;');
        $this->addSql('ALTER TABLE pages_img ADD CONSTRAINT img_page_id FOREIGN KEY (page_id) REFERENCES `pages` (id) ON DELETE NO ACTION ON UPDATE CASCADE;');
        $this->addSql('ALTER TABLE pages_lang ADD CONSTRAINT page_lang_page_id FOREIGN KEY (page_id) REFERENCES `pages` (id) ON DELETE CASCADE ON UPDATE CASCADE;');
        $this->addSql('ALTER TABLE photos_albums
            ADD CONSTRAINT album_category_id FOREIGN KEY (category_id) REFERENCES photos_cat (id) ON DELETE NO ACTION ON UPDATE CASCADE,
            ADD CONSTRAINT album_user_id FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE SET NULL ON UPDATE CASCADE,
            ADD CONSTRAINT picture_ref FOREIGN KEY (ref_picture) REFERENCES photos_img (id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->addSql('ALTER TABLE photos_cat ADD CONSTRAINT cat_parent_id FOREIGN KEY (parent_id) REFERENCES `photos_cat` (id) ON DELETE NO ACTION ON UPDATE CASCADE;');
        $this->addSql('ALTER TABLE photos_img ADD CONSTRAINT img_album_id FOREIGN KEY (album_id) REFERENCES photos_albums (id) ON DELETE CASCADE ON UPDATE CASCADE;');
        $this->addSql('ALTER TABLE sessions ADD CONSTRAINT session_user_id FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE ON UPDATE CASCADE;');
        $this->addSql('ALTER TABLE upload ADD CONSTRAINT upload_user_id FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE SET NULL ON UPDATE CASCADE;');
        $this->addSql('ALTER TABLE view_users ADD CONSTRAINT view_user_id FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE ON UPDATE CASCADE;');
    }

    public function down(Schema $schema) : void
    {
    }
}
