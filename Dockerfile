FROM php:7.2-cli

# System setup
ENV dir /home/app

RUN useradd -ms /bin/bash app
RUN apt-get install git

RUN curl -s https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

COPY . ${dir}

RUN chown -R app: ${dir}

WORKDIR ${dir}

USER app

RUN composer require -o jwplayer/jwplatform

CMD [ "php", "examples/upload_video.php" ]
