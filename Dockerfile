FROM php:7.2-cli

# System setup
ENV dir /home/app

RUN useradd -ms /bin/bash app

RUN curl -s https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

COPY . ${dir}

RUN chown -R app: ${dir}

WORKDIR ${dir}

USER app

RUN composer install
RUN composer require jw-player/jwplatform-php

CMD [ "php", "examples/upload_video.php" ]
