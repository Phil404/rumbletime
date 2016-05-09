Hello,
my name is Philipp and this is my project for the riot api-challenge 2016.
I done this my first time :)

# What is Rumbletime?
Rumbletime is a website based on League of Legends. 
Summoners can see there enemys and information of them on one view. 
You can see their skill on the picked champion and tips to fight against them.
This "app" is really minimalistic, but have a clean overview.

# Basicinformation about the project
I used different programminglanguages for this Site.
For the backend i used php (my favorite language) and for the frontend html, css and javascript.
The data of the different players comes from the riot api.
For the identification of the championskill is based on the championmasterylevel and points.

# How works this website?
First you enter your summonername or any summonername who is in game and select your region.
Than the server is going to fetch all information from the API.
It starts with the information of the game and than it goes deeper.
For this site are the important fact: championname, tips against this champion, summonername, summoner rankeddetails and summonerspells.
When all information are ready to use, the site builds up.
Important to know is the gamemode, because the option to ban champions.
If this done the summoners get visualize. 
This means that the buttons on the right and left site and the information for the center get created.
The information for the center get created in the buttons, because they are a javascript click event.
A clickevent is very important to secure an optimal userexpierience.
Finally the site is finished and you can view each summoner with useful information.

# What are the problems?
A huge problem is the loading time. 
This is because the apikey allow only 10 request per 10 secs. 
If this project would go public, this shouldnt be a problem anymore.
An other "small" problem is the definition of the championskill, because the classification is based on the championmastery.
For a better classification it needs the average game stats in connection of the average stats of all summoners.

# The future of rumbletime
I will try to improve this project and integrate it into my main project.
<br><br>
have a great day<br>(sorry for my bad english :/ )
