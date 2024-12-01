-- Tworzenie tabeli użytkowników
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    wood INT NOT NULL,
    stone INT NOT NULL,
    gold INT NOT NULL
);

-- Dodawanie przykładowych użytkowników z zasobami
INSERT INTO users (username, password, wood, stone, gold) VALUES
('Maciej', 'haslo1', 100, 200, 50),
('Piotr', 'haslo2', 150, 300, 80),
('Agata', 'haslo3', 200, 400, 100);

-- Tworzenie tabeli potworów
CREATE TABLE IF NOT EXISTS monsters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    type VARCHAR(50) NOT NULL,
    attack INT NOT NULL,
    def INT NOT NULL
);

-- Dodawanie przykładowych potworów
INSERT INTO monsters (name, type, attack, def) VALUES
('Troll', 'Ogrowate', 10, 15),
('Gryf', 'Hybrydy', 25, 22),
('Golem', 'Magiczne', 22, 40);

