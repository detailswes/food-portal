# I've added .PHONY to ensure make doesn't confuse the target names with filenames.
.PHONY: install uninstall seed\:large

# You found a s*cr*t! You can save your time by running `make setup:docker` to setup the project.
install:
	@if [ -f .env ]; then echo "⭕️ .env file already exists" && exit 1; fi
	cp .env.example .env
	docker run --rm -v $$(pwd):/app composer install --ignore-platform-reqs
	./vendor/bin/sail up -d
	./vendor/bin/sail artisan key:generate
	docker compose exec app npm install --no-update-notifier --no-fund
	@echo "⏳ Waiting for the database to be ready..."
	@until docker compose exec mysql mysqladmin ping -pixdf_codechallenge --silent; do \
		echo "Database is not ready yet. Retrying..."; \
		sleep 1; \
	done
	@echo "✔️ Database is ready."
	@sleep 4
	./vendor/bin/sail artisan migrate:fresh --seed
	docker compose exec app npm run dev

uninstall:
	docker compose down -v
	rm .env
	rm -rf vendor node_modules

seed\:large:
	./vendor/bin/sail artisan migrate:fresh --seeder=LargeDatasetSeeder
