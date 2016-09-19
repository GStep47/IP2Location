# IP2Location
This code is a demonstration of how the IP2Location databases could be used in conjunction with MySQL to create a tool that determines the location of a website visitor.

It uses the sample version of their database db3-ip-country-region-city.text database, available at https://www.ip2location.com/samples/db3-ip-country-region-city.txt. The code could be adapted support the other types of databases they offer. 

The sample database has a very limited range of IP addresses. This demonstration displays your IP address and queries the database for it. If your IP address is not in the demo range (which it probably isn't), it generates a random IP address that is within the range and queries that, just to demonstrate how the database query works. This code could be adapted to select a default city/state if the database lookup fails for any reason.

Their database stores IP addresses as a number, so the code does the necessary converting, as per https://www.ip2location.com/docs/db3-ip-country-region-city-specification.pdf. See  also https://www.ip2location.com/databases/db3-ip-country-region-city for further discussion for further discussion from ip2location as to the workings of their product.

My database also converts the state/province name from the full name ("Nebraska") to its two-letter code ("NE"), as this code was written to support an API that required two-letter codes.
