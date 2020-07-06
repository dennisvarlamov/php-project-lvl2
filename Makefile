install:
		@composer install
lint:
		@composer run-script phpcs -- --standard=PSR12 bin 
changeLintMistake:
		@composer run-script phpcbf  -- --standard=PSR12 bin 