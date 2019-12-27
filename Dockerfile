FROM php:7.2-cli

# System setup
ENV dir /home/app

RUN useradd -ms /bin/bash app

COPY . ${dir}

RUN chown -R app: ${dir}

WORKDIR ${dir}

USER app

CMD [ "php", "examples/analytics_query.php" ]
