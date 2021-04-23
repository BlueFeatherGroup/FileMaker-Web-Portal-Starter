<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://www.bluefeathergroup.com/wp-content/uploads/2020/12/logo-e1606865175629.png" width="400"></a></p>

# Customer Portal Starter Solution

## About this portal

This portal was created by [Blue Feather](https://www.bluefeathergroup.com). It was designed both as a starting point for us to use with our clients and as a resource for anyone in the FileMaker community to use for building their own web portal.

This application is a basic starting point for building a client portal for your FileMaker database. It is designed to be an example of a simple implementation with easy access to make changes and customizations for your specific business use case. If you don't know any PHP, JavaScript, HTML, or CSS you can still connect this to your DataAPI-accessible FileMaker solution just by adjusting a few configuration files and replacing the logo images!

A web portal built with traditional web technologies can be more performant than using FileMaker WebDirect and can provide users with a more familiar web experience. Frameworks like Laravel and Vue used in this solution also allow you to easily take advantage of third-party packages for creating features which you might not be able have in native FileMaker. Licensing for WebDirect can also be pricey and WebDirect can require a beefy server. A traditional stack web application like this can help control licensing and server costs.

The basic pre-built features of this portal allow you to share invoices and take payments online as examples, but it's not necessary to use those features at all. Use this as a starting point to get connected and provide customers and clients with the access you want to give them. You can add or remove any of the features which come preconfigured. 

## What can I do with this portal?
This portal comes with some pre-built features for basic invoice payments online, but you don't need to stick with that use case at all! The implementation and sample file should give you a good starting point for building your own features into the system. They don't need to be related to invoices or payments at all!

#### Some ideas might be:
* Collect onboarding information for new customers or clients
* Send or receive documents
* Allow customers to submit orders and requests
* Give status updates or reports on work performed
* Show a schedule of appointments
* Display survey results
* Provide access or collect information from employees who don't normally use FileMaker

#### Here are some of the features this starter solution comes with out of the box:
- Private data behind user logins, including two-factor authentication
- Linking of users to customer records in your FileMaker database via email matching or (optionally) invoice information
- Easy configuration to connect to your FileMaker database, preconfigured to work with the Invoices starter solution as an example
- Show paid and unpaid invoices with pagination
- Printable invoice view
- Support for taking credit card and PayPal payments through Braintree or Stripe
- Simple toggles to either allow partial payments or require full payments

## Getting Help

If you'd like assistance configuring this portal or customizing it for your business, please [contact Blue Feather](https://www.bluefeathergroup.com/contact) and we'd be happy to help you with your project as part of our consulting services. We love making portals and working on FileMaker databases!

## Application Setup and Configuration

This app is a standard Laravel application. If you're familiar with deploying a Laravel app you don't need to read the instructions below and can proceed with the normal Laravel configuration process. If you're new to things like using dependency managers and compiling JavaScript, read on!


### App Directory Location
From the [Laravel documentation](https://laravel.com/docs/8.x/installation#directory-configuration):
>Laravel should always be served out of the root of the "web directory" configured for your web server. You should not attempt to serve a Laravel application out of a subdirectory of the "web directory". Attempting to do so could expose sensitive files that exist within your application.

You want the `public` folder to be the web root of your public site, not the application root. Be sure to configure your web server to point to this location!

### Required Tools

Deploying this application requires [Composer](https://getcomposer.org/) and [Node](https://nodejs.org/en/) be installed. These two services willl be used to automatically download and install all of the required dependencies for running this application, as well as compiling JavaScript

### Get the dependencies

Once you have installed Composer and Node, and added both of them to your command path you can begin retrieving the dependencies for this application to run. Run the following command in a terminal within your project root to download PHP dependencies.

```composer install```

Do the same thing for JavaScript dependencies by running 

```
npm install
```

Compile the JavaScript and CSS with

```
npm run production
```


### Database Configuration

This solution needs a simple SQL database in addition to your FileMaker database. If you don't have a SQL server available, such as [MySQL](https://www.mysql.com/), you can use [SQLite](https://www.sqlite.org/index.html) as an alternative. SQLite is a file-based database system and does not require setting up a database server.

### SQL Database Configuration

This solution uses a SQL database for storing user logins and sessions. You can configure the connection to a SQL database to be used for this by setting the `db_xxxx=` properties in the `.env` file in the root of the application.

It's best to use MySQL or another full hosted database if you have it available, but SQLite is fine for the limited use of managing user logins on a simple deployment, low-traffic site (less than ~100k hits per day). MySQL is a more performant, scalable, and reliable database solution than SQLite and significantly more robust, but for a simple portal deployment like this sample application which primarily will use FileMaker for data, SQLite will probably be fine if you don't have a MySQL server available.

If you decide to use SQLite be aware that a database.sqlite file will be stored at `/database/database.sqlite` in this application. This file is where the user logins are stored, and so you should back this file up regularly.

### Set up your .env

The .env file is the configuration for a particular deployment. Copy the `.env.example` file to `.env` and modify the values in there to suit your configuration. You'll need to enter information for your database, FileMaker connection, and Braintree/Stripe account information.

You'll also need to generate a new app key, which is used for encryption. Run the following command to generate a new app key

```
php artisan key:generate
```


### Perform database migrations

A new SQL database won't have the tables and fields needed to run this app. Database migrations are already set up to create the tables and fields required for this app. These migrations will apply to the database you've configured in the `.env` file. You can run these migrations by running the following command:

```
php artisan migrate
```

### Map your FileMaker Fields

The default field mapping is for use with the Invoices starter solution. You'll need to modify the preconfigured field mappings to work with your own solution. Change the key values in the `$fieldMapping` array as well as the `$layout` in each of the following Model files in the `/app/Models/` directory  to point to your own fields and layouts.

Modify the `$connection`, `$layout`, and `$fieldMapping` properties of the following Model files to match your FileMaker solution. Remember that each of the fields listed in these models files for mapping must be on the layouts you are specifying for them to be available through the Data API.

```
Customer
Invoice
LineItem
```


### Adjust your portal configuration

You'll need to adjust the features of the sample portal to match what you want to have enabled. The configuration options are documented and controlled through the `/config/portal.php` file.

Configuration options configuration:

* Your company's name and address
* Select Braintree or Stripe for payments (defaults to Braintree)
* Payment alert email delivery address
* Payment success FileMaker script name and layout
* Fields which should be searched against to match customers by email addresses
* Allow users to link logins to customers in your database by matching invoice numbers and amounts if their email isn't already in your system
* Allow partial payments of invoices
* Show/hide tax fields on invoices
* Payments with PayPal through Braintree (your account must be configured for this)

## Configuration Complete!

I know it's been a lot to read through, but that's all the options and configuration for this starter solution! If you've set the right privilges for your FileMaker database login and have all the right fields on your layout you should now be able to create an account and log in to see data from your FileMaker database!

## Technologies used in this solution

This customer portal starter solution uses the technologies in the list below. These packages were selected for their ease of use and powerful customization and features. Read more about them at the links below to find out about all of the features that each of these packages come with to and to learn how to add more features to this system.

* [PHP](https://www.php.net/) - The server-side language used in this application 
* [Laravel](https://laravel.com/) - The PHP application framework used to for security, database access, session management, and much more.
* [Eloquent FileMaker](https://github.com/BlueFeatherGroup/eloquent-filemaker) - PHP package for Laravel which adds compatibility for FileMaker access through the data API with Laravel's Eloquent query builder. Both Eloquent FileMaker and this starter portal were created by [Blue Feather](https://www.bluefeathergroup.com) (Hey! That's us!).
* [Laravel Jetstream](https://jetstream.laravel.com/2.x/introduction.html) - A package by the creators of Laravel for easily getting started with user logins, password resets, 2-factor authentication, and other basic account management features. 
* [Inertia](https://inertiajs.com/) - A package used by Jetstream to interface between the back-end and front-end applications
* [Vue](https://vuejs.org/) - A front-end JavaScript framework for 
* [Tailwind](https://tailwindcss.com/) - A CSS framework for unbiased, fast styling of your application using utility CSS classes
