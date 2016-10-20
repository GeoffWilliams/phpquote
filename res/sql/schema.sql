CREATE TABLE quote (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    author      VARCHAR NOT NULL,
    quote       VARCHAR NOT NULL,
);

INSERT INTO QUOTE (author, quote) VALUES ('Legendary', 'You\'ve got to speculate to accumulate');
INSERT INTO QUOTE (author, quote) VALUES ('Yoda', 'Do. Or do not. There is no try.');

