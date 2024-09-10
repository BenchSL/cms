# cms
cms project

Setup postgres database steps: 
CREATE DATABASE cms;
CREATE USER cms WITH PASSWORD 'cms';
ALTER DATABASE cms OWNER TO cms;
GRANT ALL PRIVILEGES ON DATABASE cms TO cms;

setup stuff on google dashboard and download credentials.json file and place it into /cms/storage/

documentation for setting up Google dashboard can be found on https://myaccount.google.com/dashboard
