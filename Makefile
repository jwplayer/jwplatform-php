.PHONY: run
run:
	docker build  --tag jwplayer/php .
	docker run --init -it --rm --name jwplayer-php-example jwplayer/php
