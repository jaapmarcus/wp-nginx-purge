# WP-NGINX-PURGE

Purge Wordpress with ngx_http_cache_purge module for Nginx 

This plugin **only** works with the **ngx_http_cache_purge** module for Nginx (Nginx Plus or Custom build is **required!**)

The plugin it self has been design in combination with HestiaCP + a few custom modules but should also work on any other control / "bare" system with the correct setup.

## Setup 

- Make sure you have ngx_http_cache_purge enabled for your nginx configuration
- For Proxy cache add proxy_cache_purge PURGE from all;  to the same block as proxy_cache {key}
- Activate plugin

