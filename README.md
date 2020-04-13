# Flashcards-Plugin for DokuWiki

Converts some content into a set of scrollable/playable flashcards. Does NOT support the Internet Explorer!

## Usage

Enclose the flashcards with ``<cards>...</cards>``. The cards are separated with a horizontal line ``----``.

Example:

```
<cards>
First card
----
Second card
----
Third card
</cards>
```

## Issues / Ideas

* Improve scrolling / height settings
* Direct editing of the flashcards

### Learning System

Integration of some sort of simple learning-repititon algorithm. Simples concept would be to let the user evaluate 
if the session was -easy/medium/hard- an thus to adjust a learning level accordingly by -1/0/+1 and calculate the
next learning time by the absolute level. An automatic adjustment by score passing the first round ist not
a good concept, as there may be questions which are not important to the user.


#### Alternative 1: More work, probably better code

* user (thus must be logged in)
* filename
* absolute level
* next learning date (=earliest date to begin learning)

Integration of move-plugin ist essential!

#### Alternative 2: Simple to code

Create user-based pages, for instance ``flashcard:userpage``, where auto-lists are created with dates, 
showing when to repeat next. Addition of a sort button on that page.

Idea: (comma-separated lists)
```
  * <wikilink-to-page>, <lvl>, <next repetition YYYY-MM-DD>
```
