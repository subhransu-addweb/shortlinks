# shortlinks

Create short links like (like bit.ly)

## Getting Started

These instructions will get you a copy of the project up and running on on the server.

### Prerequisites

Need a Drupal8 instance with proper setup.

```
Any local host url or any domain name. Eg:- http://weblocal.test
```

### Installing

A step by step guide to install the module

```
1. Download the project from the github
2. The downloaded zip will named as shortlinks-master.zip
3. Extract the zip file and rename the directory to "shortlinks"
4. Copy the module directory to the custom modules directory in ypur Drupal 8 project
5. After placing the module please enable the module using the Drupal interface or use Drush command eg:- drush en shortlinks
```

After sucessfull installation it will create 2 table in the Drupal project database as mentioned below. 

```
a. shortlinks
b. shortlinks_redirects
```

## setup the custom block

After installation the module, it will create a custom block in the project named as :- Shortlink block

```
a. Navigate to the block like - <project url>admin/structure/block
b. Place the shortlink block inside any region as per your convinient and setup the pages to shown on the front page.
```

### Running the project

After all the setup the block will be shown on the front page. So inside the block there will be a form having a text field named as "Web address".
From here user can create any short links from the long URL.

### setup the rest API

After sucessfull installation the module it will create a REST resource in the project.

```
You need to enable the REST resource (Create Shortlinks resource)from the REST UI.

set Methods to "POST", Accepted request formats to "json" and Authentication providers to "basic_auth".

After proper setup the REST resource can be access by this url - <project url>/api/create-shortlinks-resource

To consume the REST you need to pass the Drupal user credentials as basic authontication

To create the short link you need to pass the body in the REST api as json format eg :- { "web_addr" : "yahoo.com"}
```

## Authors

* **Subhransu sekhar**
