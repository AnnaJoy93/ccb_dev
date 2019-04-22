# Back-End Developer Test

## Overview
Your client has tasked you with making a RESTful API to support the ultimate Moviegoers Guide suite of applications.
They are asking for an API that serves up:

* A list of movies
  * The user should be able to search the movies by title
  * The user should be able to filter the movies by rating
  * The user should be able to filter the movies by category
* Movie details for each movie
* A list of actors in a movie

We have provided a mysql database of movie info for you.
We have also provided a starter PHP application using the SLIM framework although you are free to use the language and framework of your choice - Ruby, Node, etc.

### Keep in mind the following questions

* What design patterns did you use?
* How would you test your application?
* What ways could you structure the code to make it easy to understand and maintain?
* What other considerations and tradeoffs did you make when building the application?

## Installation

### Docker
You will need Docker installed.
Here are the instructions to install Docker for ([Mac](https://docs.docker.com/docker-for-mac/install/)) or ([Windows](https://docs.docker.com/docker-for-windows/install/)). For older systems that do not meet the docker requirements, use the [Docker Toolbox](https://docs.docker.com/toolbox/overview) installation instructions.

_NOTE if using Docker Toolbox_: To verify you may have to use the ip address of the docker machine. In the _verify installation_ section you will want to use the ip address returned by `docker-machine ip` instead of localhost.

### Composer

You will also need to install Composer if you plan to use our starter PHP application.
Here are the instructions to install [Composer](https://getcomposer.org/download)

Launch the test code with the following commands

```
cd developer_test_server/movies
composer install
cd ../
docker-compose up -d
```

### Verify Installation

In a browser navigate to http://localhost:3000/movies. You should see ```[]``` returned from the request.

_Note_: If you don't see the empty array, run ```docker-compose logs``` and wait for all the containers to be fully loaded. Once the containers are completely loaded you should be able to hit the movies endpoint.

### Database Connection Information

__host__: 127.0.0.1<br />
__username__: sakila<br />
__password__: sakila<br />
__database__: sakila<br />
__port__: 3306

## Submitting Your App
When you have completed your app, please post it in a public repository and send us a link - GitHub, GitLab, BitBucket etc.


## Anna's Notes

### Possible Routes

Get Movies
Route: /movies
Accepts Params: title, category, rating
Example: http://localhost:3000/movies?title=bear&rating=r&category=Children
Details: Will return a list of movies with basic info. Params are optional, but if used will search by title, and apply inclusive fiters for category and/or rating. If a filter request does not match an existing option (ex. category=notacategory) then it will be ignored.

Get Movie Details by ID
Route: /movies/details
Accepts Params: film_id
Example: http://localhost:3000/movies/details?film_id=3
Details: Will return movie details for the movie with the provided ID. If film_id param is not included then will
return null.

Get Actors by Movie ID
Route: /actors
Accepts Params: film_id
Example: http://localhost:3000/actors?film_id=7
Details: Will return a list of actors in the movie with the provided ID. If film_id param is not included then will
return null.

### Some design decisions
Goal was to have one DataLoader class to interface with the database. PDO accomplishes that to a degree, but this would be more specific to this app. Eventually, once auth and session handling is setup that would pair nicely with a some sort of data cache and tracker classes - things to track requests and store common/recent searches. 

I also built in a factory method to handle filtering. Right now it's only setup for option based filters, but could be expanced, possibly with a iFilter interface for the different types of filters. The thought there was to quickly be able to quickly use the factory to dynamically create a SQL fliltering string. 

### Some things that were not included
- User Authentication
- Things that go along with authentication like session handling, request tracking, and caching
- Database tuning - including stored proceedures and indices
- Unit testing framework, setup some basic tests but not an actual framework

### Testing
The easiest way to test is to hit the /test/dataloader endpoint. As mentioned above, I didn't set up a formal unit testing framework, but I did write up a few of my own tests. They aren't really unit tests as they also test the database, but they were what I found to be helpful.

Note: These also don't directly test enpoints. They test the query methods that are directly called by the endpoints.


