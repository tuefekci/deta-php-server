![image](https://repository-images.githubusercontent.com/621638232/41d10563-9e61-49aa-b1d0-0ffa1e054a35)

# WIP! PRERELEASE! deta-php-server aka DETAPHANT!
PHP Version: 8.2.4

This is a simple server for php projects that uses the deta micros platform. I can be used to host small to medium php Websites but remember micros are serverless so most finished scripts will not work without making changes. Only /tmp is writeable an already the Webroot the rest is read only. Keep the files in the Webroot as small as possible and use my Deta Api Wrapper to store files in the Deta Drive & Base and not in the standard Webroot if possible, every MB or file more will delay your coldstarts but most stuff works fine without any changes.

There is also the standart mode which minimizes coldstarts by only syncing the files that need to be executed and not the whole project. This is the default mode and is recommended for most projects, only switch to full sync if you have problem. It works by keeping a list of all files in the project that are static and only syncing the files that are not in the list. This is not perfect and can cause problems if you have a lot of special file type files in the project but it works fine for most projects.

(don't use this in production, it's not secure and it's not meant to be used in production!) 
(also it's not finished yet) 
(also it's not meant to be used in production) 
(also why does this exists?)

## Features
- Full Sync (syncs all files in the project) (when linking is out also syncs files with ide)
- PHP.ini (you can change the php.ini in the settings)
- Logs (you can see the logs in the settings)
- Stats (you can see the stats in the settings)
- Filemanager (you can see the filemanager in the WebUI)
- Express based middleware which serves static files, drive files and routes requests to the php files
- Deta Api Wrapper (you can use the Deta Api Wrapper to store files in the Deta Drive & Base and not in the standard Webroot if possible)
- Cron/Action Support
- Standart Mode (minimizes coldstarts by only syncing the files that need to be executed and not the whole project)
- Deta Space Support (you can use the Deta Space to host your project)
- Deta Space Settings (you can change the settings in the Deta Space)

... and more

## How to use
- Install via Deta.Space (https://deta.space)
- Open the project in the Deta Space and change Settings
- The rest is in the Interface.

## What does not work yet or will never work
- Sessions (due to the serverless platform this is kinda logical use cookies or jwts in localstorage instead)
- SSL (need to manually figure out the certificate and key)

## What will absolutely not work
- Wordpress
- Everything that has fixed paths outside of the workroot which is /tmp
- 

## What works
- Everything else (I think)
- If you try you can do a lot with what we have here for demos and small projects its perfect but keep in mind that max execution time is around 20secs and possible memory is around 400mb but PHP is good at optimizing so you can do a lot with that.

## Todo:
- Check out php file mergin via ast
- Implement composer support outside of build process.
- Implement a way to use the deta drive as a file cache / monkeypatch php file functions or use a stream wrapper but that would be slow and im not sure if it would work.
- Implement a better upload than via webinterface S3 or WebDAV should work otherwise write a small companion tool for uploads there is already a cli tool on github and its not really complicated.
- Check better ways for the deta auth.
