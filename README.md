# netfoxmaking/netcms

NetCMS is an opensource project developed in PHP7.4 using the Symfony 5 framework.

## Features

* Home page containing a general vision of the website
* Article management
* Customizable multilingual pages
* Photo gallery by categories / album
* View counter
* Comments for article / gallery
* Sponsor / partnership
* Downloadable document
* Enabling/Disabling of page / partner / album
* Contact
* Search by keyword
* Member area: Registration / login / profile
* Administration with user rights
* Multi language support (FR/EN)
* FeatureFlipping
* OpenGraph


## Libs & frameworks

### PHP
* Symfony 5
* Flysystem: Storage management (local, ftp, S3, GC Storage...)
* Intervention-image: Image resizing for better cache performances
* Messenger: CQRS support with an asynchronous use evolution if necessary
* Twig: Rendering / Templating
* Doctrine DBAL

### Front
* MDBootstrap: Bootstrap overlay with material design theme
* CKEditor5: Text editor used by articles / pages
* Dropify: File / image upload in forms
* Lightbox: Gallery usage
* Slick: Content slider used for partners

### OPS
* Docker & Docker-compose
* MySQL + PhpMyAdmin
* PHP7.4-fpm + OPCache + APCU + xdebug
* Nginx

## Structure / architecture / patterns

### DDD (Domain Driven Development)
The first choice was to separate all the functionalities by domain.  
For example Article, User, Partner, Page are four domains which have no relation between them and it would not have been wise to group them.  
Two solutions were possible, making micro-services, but the goal of the project was that anyone could maintain the project easily, so it was therefore not the most suitable solution.  
The second solution was to use DDD (Domain Driven Development), which is a good compromise for a monolithic architecture.

### Hexagonal architecture
The goal was also to think about what is possible to do now and in the future, for example currently the front is managed by the same Symfony application, but if we wanted to offer HTTP APIs? Will we have to develop the same business logic twice?  
It is the same for databases, we currently use MySQL, but if we wanted to add layers of cache with Redis or ElasticSearch?  
The answer is clear, the use of a hexagonal architecture perfectly met expectations and pairs perfectly with other patterns.

### CQRS (Command Query Responsibility Segregation)
Having a vision of writing in "workflow" mode, and having chosen to use a hexagonal architecture.  
It is clear that writing had to be isolated, the use of the messenger component to create my own "command" bus.  
But what about reading?  
I also created a "query" bus, but my choice of its use was not totally CQRS.  
The use of the query is only for cases that come back regularly or cases where there is data aggregation.  
Using the CQRS term 100% would have been a waste of time or rather unnecessary complexity in my case.

### EDA (Event-driven architecture)
Symfony is a very good framework with very good event management.  
Sometimes, during an action (most often related to writing), it is necessary to perform additional actions.  
For example in the case of deleting an user, we also want the comments to be deleted, but it is not the responsability of the user to do so, because he does not have to know anything other than his field.  
The user must therefore report that it has been deleted, and other domains can subscribe to this type of event in order to perform actions.


## Use cases

* The first client was a sports association, which needed to share content, such as articles, where users can be notified; photo albums to keep the good moments, but also to share information about the club.
* Bed and breakfast, with sharing of information and photos, but the most important is multi-language support because customers are very often international. The possibility to contact in order to reserve a room.
* Share your travel stories: write your weekly adventures, share your photos classified by country / city and advise your fans.


## Getting started

### Requirements
* Docker
* Docker-compose

### Setting Up
1. First, clone the project `git clone https://github.com/faille76/netcms.git`
2. Build and run: `docker-compose up --build -d`
3. Download vendors and initialize symfony `docker-compose exec php composer install`
4. Database initialization `docker-compose exec php php bin/console doctrine:migration:migrate`

### Launch it
* The CMS is accessible on http://symfony.localhost/ (Username: admin / Password: admin)
* You can also access phpmyadmin on http://phpmyadmin.localhost/

### More configuration
The feature flipping and CMS parameters are configurable from the administrator interface via the platform.  
Configuration of the application in the .env file


## Infrastructure

### Local
Locally, you just need to use docker compose, it doesn't require any configuration or installation.

### Hosted on-premise
If you don't want to use docker-compose, you must first install php7.4 and imagick. For more information, see the docker fpm image in this repository.

### Cloud-friendly
With Google Cloud Platform, you have the possibility at a very reduced price and only for use, to deploy instances of the application with App Engine. For statics (assets & content), you can use Google Cloud Storage Bucket and configure FlySystem.


## Author
* Frederic Oddou <frederic1.oddou@gmail.com> - [Resume](https://frederic-oddou.pro) - [LinkedIn](https://www.linkedin.com/in/fredericoddou/)


## Code license
You are free to use the code in this repository under the terms of the 0-clause BSD license. LICENSE contains a copy of this license.