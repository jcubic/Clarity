FROM php:8.3-cli

RUN apt-get update && apt-get install -y unzip git && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "web", "web/index.php"]
