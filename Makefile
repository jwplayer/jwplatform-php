.PHONY: run
run:
	docker build  --tag jwplayer/php .
	docker run --init -it --rm \
	  --env JWPLATFORM_API_KEY=${JWPLATFORM_API_KEY} \
	  --env JWPLATFORM_API_SECRET=${JWPLATFORM_API_SECRET} \
	  --env JWPLATFORM_REPORTING_API_KEY=${JWPLATFORM_REPORTING_API_KEY} \
		--name jwplayer-php-example jwplayer/php
