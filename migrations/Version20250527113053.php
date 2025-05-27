<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250527113053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE book_user DROP FOREIGN KEY FK_940E9D4116A2B381
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book_user DROP FOREIGN KEY FK_940E9D41A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE book_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3313256915B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_CBE5A3313256915B ON book
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book DROP relation_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE book_user (book_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_940E9D4116A2B381 (book_id), INDEX IDX_940E9D41A76ED395 (user_id), PRIMARY KEY(book_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book_user ADD CONSTRAINT FK_940E9D4116A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book_user ADD CONSTRAINT FK_940E9D41A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book ADD relation_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book ADD CONSTRAINT FK_CBE5A3313256915B FOREIGN KEY (relation_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CBE5A3313256915B ON book (relation_id)
        SQL);
    }
}
