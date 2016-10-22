CREATE TABLE IF NOT EXISTS quote (
  id MEDIUMINT NOT NULL AUTO_INCREMENT,
  quote VARCHAR(255) NOT NULL,
  author VARCHAR(255) NOT NULL,
  ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
--  id  INTEGER NOT NULL AUTOINCREMENT
--    author      VARCHAR(255),
--    quote       VARCHAR(255)
--);


INSERT INTO quote (author, quote) VALUES ('Legendary', 'You\'ve got to speculate to accumulate');
INSERT INTO quote (author, quote) VALUES ('Yoda', 'Do. Or do not. There is no try.');

