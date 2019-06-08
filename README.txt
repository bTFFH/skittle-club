Already existing user (login/password) - tester/test123
You can always create a new user (btw, password adam123 never works, dont really know why)

!!!ATTENTION!!!

While import or create database (dump of skittle_club.txt, .txt because git does not really likes default sql line endings what I dont want to fix) it should be named as "skittle_club" or change it in root/helpers/dbConnOpen.php with other settings
Required site directory name: IndZ (or change almost in all files c:)


Directories:
	actions
		Pages with any actions that causes insertions to database
	helpers
		Supportiong scripts such as error handler, db connecter etc (manage dbConnOpen.php to set connection settings)
	images
		Images, logo
	info
		Information pages which show general tables (teams, players etc)
	reports
		Pages which show necessary reports
	stats
		Statistic pages, mainly working as info pages
	styles
		Directory with css styles that mainly linked up in helpers scripts

Main pages:
	index.php
		Main page causing LogIn
	registration.php
		Page to register new users
	welcome.php
		Dummy page that greets LoggedIn or just registered users
