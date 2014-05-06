Social-graph
============

A social graph developed with zend framework

the project is developed using ZF, so it is based on MVC design.
The model is composed of two classes, Contact and City. The first one contains the methods to obtain friends, 
friends of friends ad suggested friends data. The second one contains the methods to get the suggested cities
based on the social graph. The algorithm gets the visited cities of friends and of friends of friends,
excludes the duplicates and the cities already visited by the user and sort them by percentage. 

