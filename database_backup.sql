CREATE TABLE `administrateur`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(100) NOT NULL,
    `prenom` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `mot_de_passe` VARCHAR(255) NOT NULL,
    `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP());
ALTER TABLE
    `administrateur` ADD UNIQUE `administrateur_email_unique`(`email`);
CREATE TABLE `secteur`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `libelle` VARCHAR(100) NOT NULL
);
ALTER TABLE
    `secteur` ADD UNIQUE `secteur_libelle_unique`(`libelle`);
CREATE TABLE `ville`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `libelle` VARCHAR(100) NOT NULL
);
ALTER TABLE
    `ville` ADD UNIQUE `ville_libelle_unique`(`libelle`);
CREATE TABLE `type_contrat`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `libelle` VARCHAR(100) NOT NULL
);
ALTER TABLE
    `type_contrat` ADD UNIQUE `type_contrat_libelle_unique`(`libelle`);
CREATE TABLE `candidat`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(100) NOT NULL,
    `prenom` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `mot_de_passe` VARCHAR(255) NOT NULL,
    `telephone` VARCHAR(30) NOT NULL,
    `id_ville` INT UNSIGNED NOT NULL,
    `cv_fichier` VARCHAR(255) NULL,
    `statut` ENUM('actif', 'desactive') NOT NULL DEFAULT 'actif',
    `date_inscription` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP());
ALTER TABLE
    `candidat` ADD UNIQUE `candidat_email_unique`(`email`);
CREATE TABLE `entreprise`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NULL,
    `telephone` VARCHAR(30) NULL,
    `adresse` VARCHAR(255) NULL,
    `id_secteur` INT UNSIGNED NOT NULL,
    `id_ville` INT UNSIGNED NOT NULL,
    `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP());
CREATE TABLE `offre`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `titre` VARCHAR(200) NOT NULL,
    `description` TEXT NOT NULL,
    `salaire` DECIMAL(12, 2) NULL,
    `date_limite` DATE NOT NULL,
    `statut` ENUM('brouillon', 'publiee', 'cloturee') NOT NULL DEFAULT 'brouillon',
    `id_entreprise` INT UNSIGNED NOT NULL,
    `id_secteur` INT UNSIGNED NOT NULL,
    `id_ville` INT UNSIGNED NOT NULL,
    `id_type_contrat` INT UNSIGNED NOT NULL,
    `date_publication` DATETIME NULL
);
CREATE TABLE `candidature`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_candidat` INT UNSIGNED NOT NULL,
    `id_offre` INT UNSIGNED NOT NULL,
    `lettre_motivation` TEXT NOT NULL,
    `statut` ENUM('en_attente', 'retenue', 'refusee') NOT NULL DEFAULT 'en_attente',
    `date_candidature` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP());
ALTER TABLE
    `candidature` ADD UNIQUE `candidature_id_candidat_id_offre_unique`(`id_candidat`, `id_offre`);
ALTER TABLE
    `offre` ADD CONSTRAINT `offre_id_entreprise_foreign` FOREIGN KEY(`id_entreprise`) REFERENCES `entreprise`(`id`);
ALTER TABLE
    `candidat` ADD CONSTRAINT `candidat_id_ville_foreign` FOREIGN KEY(`id_ville`) REFERENCES `ville`(`id`);
ALTER TABLE
    `candidature` ADD CONSTRAINT `candidature_id_candidat_foreign` FOREIGN KEY(`id_candidat`) REFERENCES `candidat`(`id`);
ALTER TABLE
    `offre` ADD CONSTRAINT `offre_id_type_contrat_foreign` FOREIGN KEY(`id_type_contrat`) REFERENCES `type_contrat`(`id`);
ALTER TABLE
    `candidature` ADD CONSTRAINT `candidature_id_offre_foreign` FOREIGN KEY(`id_offre`) REFERENCES `offre`(`id`);
ALTER TABLE
    `entreprise` ADD CONSTRAINT `entreprise_id_secteur_foreign` FOREIGN KEY(`id_secteur`) REFERENCES `secteur`(`id`);
ALTER TABLE
    `entreprise` ADD CONSTRAINT `entreprise_id_ville_foreign` FOREIGN KEY(`id_ville`) REFERENCES `ville`(`id`);
ALTER TABLE
    `offre` ADD CONSTRAINT `offre_id_ville_foreign` FOREIGN KEY(`id_ville`) REFERENCES `ville`(`id`);
ALTER TABLE
    `offre` ADD CONSTRAINT `offre_id_secteur_foreign` FOREIGN KEY(`id_secteur`) REFERENCES `secteur`(`id`);