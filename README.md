### Setup

You'll need basic Nginx, PHP7, MySQL stack. I used PuPHPet.com to generate a Vagrant box and I have commited related files for it in `puphpet` directory. Alternatively you can use whatever you like as long as you have that stack and Nginx virtualhost is setup to point directly at public/index.php (so called beautiful URLs).

Run `composer install` to install dev dependencies, you will need to install PHP extensions for dependencies if you haven't already before. Run `composer dumpautoload` to have the class auto-loading generated for the project. I am using composers auto-loading instead of creating my own.

The database is provided in `dbdump.sql` file. `mysql -u root -p database < dbdump.sql` to import it. Change database credentials in `.env` file (`cp .env.example .env` first).
