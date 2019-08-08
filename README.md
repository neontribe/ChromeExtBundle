## Testing

```bash
docker run -ti --rm --name text-chromext -v $(pwd):/opt/kimai/var/plugins/ChromeExtBundle -p 8001:8001 kimai/kimai2:apache-debian-master
docker exec text-chromext /opt/kimai/bin/console kimai:create-user neontribe tobias+kimai@neontribe.co.uk ROLE_SUPER_ADMIN weQFGDSbjenFM5Z6mgSASJwqfX4r3OOp
```
