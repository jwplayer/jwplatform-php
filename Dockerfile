FROM php:7.2-cli

RUN useradd -ms /bin/bash app

COPY . /usr/src/myapp
WORKDIR /usr/src/myapp

USER app

CMD [ "php", "examples/upload_video.php" ]
