<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220815082658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__recipe AS SELECT id, name, person, difficulty, rate, time_preparation, time_cooking, calorie FROM recipe');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('CREATE TABLE recipe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, person SMALLINT NOT NULL, difficulty smallint NOT NULL, rate smallint NOT NULL, time_preparation SMALLINT NOT NULL, time_cooking SMALLINT DEFAULT NULL, calorie INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO recipe (id, name, person, difficulty, rate, time_preparation, time_cooking, calorie) SELECT id, name, person, difficulty, rate, time_preparation, time_cooking, calorie FROM __temp__recipe');
        $this->addSql('DROP TABLE __temp__recipe');
        $this->addSql('DROP INDEX IDX_70DCBC5F12469DE2');
        $this->addSql('DROP INDEX IDX_70DCBC5F59D8A214');
        $this->addSql('CREATE TEMPORARY TABLE __temp__recipe_category AS SELECT recipe_id, category_id FROM recipe_category');
        $this->addSql('DROP TABLE recipe_category');
        $this->addSql('CREATE TABLE recipe_category (recipe_id INTEGER NOT NULL, category_id INTEGER NOT NULL, PRIMARY KEY(recipe_id, category_id), CONSTRAINT FK_70DCBC5F59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_70DCBC5F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO recipe_category (recipe_id, category_id) SELECT recipe_id, category_id FROM __temp__recipe_category');
        $this->addSql('DROP TABLE __temp__recipe_category');
        $this->addSql('CREATE INDEX IDX_70DCBC5F12469DE2 ON recipe_category (category_id)');
        $this->addSql('CREATE INDEX IDX_70DCBC5F59D8A214 ON recipe_category (recipe_id)');
        $this->addSql('DROP INDEX IDX_22D1FE13933FE08C');
        $this->addSql('DROP INDEX IDX_22D1FE1359D8A214');
        $this->addSql('CREATE TEMPORARY TABLE __temp__recipe_ingredient AS SELECT id, recipe_id, ingredient_id, quantity, unity, note FROM recipe_ingredient');
        $this->addSql('DROP TABLE recipe_ingredient');
        $this->addSql('CREATE TABLE recipe_ingredient (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_id INTEGER NOT NULL, ingredient_id INTEGER NOT NULL, quantity SMALLINT DEFAULT NULL, unity VARCHAR(2) NOT NULL, note VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_22D1FE1359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_22D1FE13933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO recipe_ingredient (id, recipe_id, ingredient_id, quantity, unity, note) SELECT id, recipe_id, ingredient_id, quantity, unity, note FROM __temp__recipe_ingredient');
        $this->addSql('DROP TABLE __temp__recipe_ingredient');
        $this->addSql('CREATE INDEX IDX_22D1FE13933FE08C ON recipe_ingredient (ingredient_id)');
        $this->addSql('CREATE INDEX IDX_22D1FE1359D8A214 ON recipe_ingredient (recipe_id)');
        $this->addSql('DROP INDEX IDX_43B9FE3C59D8A214');
        $this->addSql('CREATE TEMPORARY TABLE __temp__step AS SELECT id, recipe_id, content FROM step');
        $this->addSql('DROP TABLE step');
        $this->addSql('CREATE TABLE step (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_id INTEGER NOT NULL, content CLOB NOT NULL, CONSTRAINT FK_43B9FE3C59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO step (id, recipe_id, content) SELECT id, recipe_id, content FROM __temp__step');
        $this->addSql('DROP TABLE __temp__step');
        $this->addSql('CREATE INDEX IDX_43B9FE3C59D8A214 ON step (recipe_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, roles, password, name FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, name VARCHAR(150) DEFAULT NULL, email VARCHAR(150) DEFAULT NULL, enabled SMALLINT DEFAULT 1 NOT NULL, expiresat DATE DEFAULT NULL, avatar VARCHAR(250) DEFAULT NULL, last_login DATETIME DEFAULT NULL, last_activity DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO user (id, username, roles, password, name) SELECT id, username, roles, password, name FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('CREATE TEMPORARY TABLE __temp__recipe AS SELECT id, name, person, difficulty, rate, time_preparation, time_cooking, calorie FROM recipe');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('CREATE TABLE recipe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, person SMALLINT NOT NULL, difficulty SMALLINT NOT NULL, rate SMALLINT NOT NULL, time_preparation SMALLINT NOT NULL, time_cooking SMALLINT DEFAULT NULL, calorie INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO recipe (id, name, person, difficulty, rate, time_preparation, time_cooking, calorie) SELECT id, name, person, difficulty, rate, time_preparation, time_cooking, calorie FROM __temp__recipe');
        $this->addSql('DROP TABLE __temp__recipe');
        $this->addSql('DROP INDEX IDX_70DCBC5F59D8A214');
        $this->addSql('DROP INDEX IDX_70DCBC5F12469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__recipe_category AS SELECT recipe_id, category_id FROM recipe_category');
        $this->addSql('DROP TABLE recipe_category');
        $this->addSql('CREATE TABLE recipe_category (recipe_id INTEGER NOT NULL, category_id INTEGER NOT NULL, PRIMARY KEY(recipe_id, category_id))');
        $this->addSql('INSERT INTO recipe_category (recipe_id, category_id) SELECT recipe_id, category_id FROM __temp__recipe_category');
        $this->addSql('DROP TABLE __temp__recipe_category');
        $this->addSql('CREATE INDEX IDX_70DCBC5F59D8A214 ON recipe_category (recipe_id)');
        $this->addSql('CREATE INDEX IDX_70DCBC5F12469DE2 ON recipe_category (category_id)');
        $this->addSql('DROP INDEX IDX_22D1FE1359D8A214');
        $this->addSql('DROP INDEX IDX_22D1FE13933FE08C');
        $this->addSql('CREATE TEMPORARY TABLE __temp__recipe_ingredient AS SELECT id, recipe_id, ingredient_id, quantity, unity, note FROM recipe_ingredient');
        $this->addSql('DROP TABLE recipe_ingredient');
        $this->addSql('CREATE TABLE recipe_ingredient (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_id INTEGER NOT NULL, ingredient_id INTEGER NOT NULL, quantity SMALLINT DEFAULT NULL, unity VARCHAR(2) NOT NULL, note VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO recipe_ingredient (id, recipe_id, ingredient_id, quantity, unity, note) SELECT id, recipe_id, ingredient_id, quantity, unity, note FROM __temp__recipe_ingredient');
        $this->addSql('DROP TABLE __temp__recipe_ingredient');
        $this->addSql('CREATE INDEX IDX_22D1FE1359D8A214 ON recipe_ingredient (recipe_id)');
        $this->addSql('CREATE INDEX IDX_22D1FE13933FE08C ON recipe_ingredient (ingredient_id)');
        $this->addSql('DROP INDEX IDX_43B9FE3C59D8A214');
        $this->addSql('CREATE TEMPORARY TABLE __temp__step AS SELECT id, recipe_id, content FROM step');
        $this->addSql('DROP TABLE step');
        $this->addSql('CREATE TABLE step (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, recipe_id INTEGER NOT NULL, content CLOB NOT NULL)');
        $this->addSql('INSERT INTO step (id, recipe_id, content) SELECT id, recipe_id, content FROM __temp__step');
        $this->addSql('DROP TABLE __temp__step');
        $this->addSql('CREATE INDEX IDX_43B9FE3C59D8A214 ON step (recipe_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, name, roles, password FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, name VARCHAR(100) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO user (id, username, name, roles, password) SELECT id, username, name, roles, password FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }
}
