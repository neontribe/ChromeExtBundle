# Deving

## Start the dev instance:

This may take some time, wait for a line like ```"Thu Aug 08 10:55:44.869316 2019] [core:notice] [pid 49] AH00094: Command line: '/usr/sbin/apache2 -D FOREGROUND'"```

```bash
docker-compose up
```

## install dev data

Just answer yes to the questions

```
docker-compose exec kimai bin/console kimai:reset-dev
```

## Symlink the test html into the public folder

```
docker-compose exec kimai ln -s /opt/kimai/var/plugins/ChromeExtBundle/neontribe-kimai-brdge /opt/kimai/public/ntce
```

## Hit the test page

You can log in as ```susan_super``` and ```kitten```

http://localhost:8001/ntce/index.html

# Useful dev URLS

 * [The static test html][http://localhost:8001/ntce/index.html]
 * [The plugin page](http://localhost:8001/en/neontribe/ext/projects)
