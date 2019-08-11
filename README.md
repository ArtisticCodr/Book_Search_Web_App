# Book Search Web App
Library web search application. The first part is importing book data from a file and preparing a search prefix. Book info is
given in a file in UNIMARC format (European standard for describing library materials - books, magazines, ...).
The search is done by prefixes and it loads from a separate file the prefix mapping to
elements of the UNIMARC format, as well as prefix names. The web search application supports
advanced multi-criteria search functionality joined by logical operators,
adding operators, saving session query history, loading session queries, usage of wildcard
characters in the query. Displaying search results supports options for displaying all data and the abbreviated
display.

# Python part
The part that is written in python imports data from files into a relational database. 
Book information is in the book file.txt is given so that each row contains information about one book in UNIMARC
the format explained in Annex 1. The field is indicated by three digits and in front of each field (except the first one)
there is a sign under number 30 from the ASCII table (in Notepad ++ is displayed as RS). After marking the box
two indicator spaces are reserved, they can be blank or with some indicator value and follow
list of subfields belonging to the field. The subfield designation begins with the character 31 from the ASCII table (in
Notepad++ it is displayed as US), followed by one character, which is the name of the subfield and its contents
subfields.
The mapping file contains the name of the prefix, the dash and the tag of the fields and subfields to which it maps, and the meaning is
next, for example mapping TI-200a means that the de string entered in the TI prefix is ​​searched in the subfield a
200.

# PHP 
The php part represents the backend of this project


# JavaScript/HTML
The javascript and html part represents the frontend of this project

# Note
This Project was written as a part of the course "Script Languages" at The Faculty Of Computer Science in Belgrade

# Contributed
Filip Hadzi-Ristic<br>
filip.h-r@protonmail.com<br>
