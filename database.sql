-- ============================================================
-- JOBLINK BENIN
-- Base de données complète
-- ============================================================

-- ============================================================
-- 1. TABLE ADMINISTRATEUR
-- ============================================================

CREATE TABLE `administrateur`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(100) NOT NULL,
    `prenom` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `mot_de_passe` VARCHAR(255) NOT NULL,
    `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP()
);

ALTER TABLE
    `administrateur`
ADD UNIQUE `administrateur_email_unique`(`email`);


-- ============================================================
-- 2. TABLE SECTEUR
-- ============================================================

CREATE TABLE `secteur`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `libelle` VARCHAR(100) NOT NULL
);

ALTER TABLE
    `secteur`
ADD UNIQUE `secteur_libelle_unique`(`libelle`);


-- ============================================================
-- 3. TABLE VILLE
-- ============================================================

CREATE TABLE `ville`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `libelle` VARCHAR(100) NOT NULL
);

ALTER TABLE
    `ville`
ADD UNIQUE `ville_libelle_unique`(`libelle`);


-- ============================================================
-- 4. TABLE TYPE DE CONTRAT
-- ============================================================

CREATE TABLE `type_contrat`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `libelle` VARCHAR(100) NOT NULL
);

ALTER TABLE
    `type_contrat`
ADD UNIQUE `type_contrat_libelle_unique`(`libelle`);


-- ============================================================
-- 5. TABLE CANDIDAT
-- ============================================================

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
    `date_inscription` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP()
);

ALTER TABLE
    `candidat`
ADD UNIQUE `candidat_email_unique`(`email`);


-- ============================================================
-- 6. TABLE ENTREPRISE
-- ============================================================

CREATE TABLE `entreprise`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NULL,
    `telephone` VARCHAR(30) NULL,
    `adresse` VARCHAR(255) NULL,
    `id_secteur` INT UNSIGNED NOT NULL,
    `id_ville` INT UNSIGNED NOT NULL,
    `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP()
);


-- ============================================================
-- 7. TABLE OFFRE
-- ============================================================

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


-- ============================================================
-- 8. TABLE CANDIDATURE
-- ============================================================

CREATE TABLE `candidature`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_candidat` INT UNSIGNED NOT NULL,
    `id_offre` INT UNSIGNED NOT NULL,
    `lettre_motivation` TEXT NOT NULL,
    `statut` ENUM('en_attente', 'retenue', 'refusee') NOT NULL DEFAULT 'en_attente',
    `date_candidature` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP()
);

ALTER TABLE
    `candidature`
ADD UNIQUE `candidature_id_candidat_id_offre_unique`(`id_candidat`, `id_offre`);


-- ============================================================
-- 9. CLES ETRANGERES
-- ============================================================

ALTER TABLE
    `offre`
ADD CONSTRAINT `offre_id_entreprise_foreign`
FOREIGN KEY(`id_entreprise`)
REFERENCES `entreprise`(`id`);

ALTER TABLE
    `candidat`
ADD CONSTRAINT `candidat_id_ville_foreign`
FOREIGN KEY(`id_ville`)
REFERENCES `ville`(`id`);

ALTER TABLE
    `candidature`
ADD CONSTRAINT `candidature_id_candidat_foreign`
FOREIGN KEY(`id_candidat`)
REFERENCES `candidat`(`id`);

ALTER TABLE
    `offre`
ADD CONSTRAINT `offre_id_type_contrat_foreign`
FOREIGN KEY(`id_type_contrat`)
REFERENCES `type_contrat`(`id`);

ALTER TABLE
    `candidature`
ADD CONSTRAINT `candidature_id_offre_foreign`
FOREIGN KEY(`id_offre`)
REFERENCES `offre`(`id`);

ALTER TABLE
    `entreprise`
ADD CONSTRAINT `entreprise_id_secteur_foreign`
FOREIGN KEY(`id_secteur`)
REFERENCES `secteur`(`id`);

ALTER TABLE
    `entreprise`
ADD CONSTRAINT `entreprise_id_ville_foreign`
FOREIGN KEY(`id_ville`)
REFERENCES `ville`(`id`);

ALTER TABLE
    `offre`
ADD CONSTRAINT `offre_id_ville_foreign`
FOREIGN KEY(`id_ville`)
REFERENCES `ville`(`id`);

ALTER TABLE
    `offre`
ADD CONSTRAINT `offre_id_secteur_foreign`
FOREIGN KEY(`id_secteur`)
REFERENCES `secteur`(`id`);


-- ============================================================
-- 10. DONNEES DE REFERENCE
-- ============================================================

INSERT INTO `administrateur`
(`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `date_creation`)
VALUES
(1, 'ADMIN', 'JobLink', 'admin@joblink.bj',
'$2y$10$aK8mH/N2RMN1FWG9104VQuxmPvDfEopyJHAlDxTlsIWeP857hF3t6',
'2026-10-02 19:35:18');


INSERT INTO `secteur`
(`id`, `libelle`)
VALUES
(1, 'Informatique'),
(2, 'finance'),
(3, 'Commerce'),
(4, 'Marketing'),
(5, 'Ressources Humaines'),
(6, 'Transport');


INSERT INTO `ville`
(`id`, `libelle`)
VALUES
(1, 'Cotonou'),
(2, 'Porto-Novo'),
(3, 'Abomey-Calavi'),
(4, 'Parakou'),
(5, 'Ouidah'),
(6, 'cocotomey');


INSERT INTO `type_contrat`
(`id`, `libelle`)
VALUES
(1, 'CDI'),
(2, 'CDD'),
(3, 'Stage'),
(4, 'Freelance');


-- ============================================================
-- 11. CANDIDATS
-- ============================================================

INSERT INTO `candidat`
(`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `telephone`,
 `id_ville`, `cv_fichier`, `statut`, `date_inscription`)
VALUES
(1, 'ADJOVI', 'Jean',
 'jean.adjovi@example.com',
 '$2y$12$H9ajz8TufxE2rfaDt10HFeaFoDeTisVa4zTgyTZqeLoXkaJ1//.U2',
 '97010001',
 1,
 'CV_Jean_ADJOVI.pdf',
 'actif',
 '2026-10-02 19:33:29'),

(2, 'HOUNKPE', 'Marie',
 'marie.hounkpe@example.com',
 '$2y$12$H9ajz8TufxE2rfaDt10HFeaFoDeTisVa4zTgyTZqeLoXkaJ1//.U2',
 '97010002',
 2,
 'CV_Marie_HOUNKPE.pdf',
 'actif',
 '2026-10-02 19:33:29'),

(3, 'KOFFI', 'Paul',
 'paul.koffi@example.com',
 '$2y$12$H9ajz8TufxE2rfaDt10HFeaFoDeTisVa4zTgyTZqeLoXkaJ1//.U2',
 '97010003',
 3,
 'CV_Paul_KOFFI.pdf',
 'actif',
 '2026-10-02 19:33:29'),

(4, 'GBEDO', 'Alice',
 'alice.gbedo@example.com',
 '$2y$12$H9ajz8TufxE2rfaDt10HFeaFoDeTisVa4zTgyTZqeLoXkaJ1//.U2',
 '97010004',
 1,
 'CV_Alice_GBEDO.pdf',
 'actif',
 '2026-10-02 19:33:29'),

(5, 'SOSSOU', 'David',
 'david.sossou@example.com',
 '$2y$12$H9ajz8TufxE2rfaDt10HFeaFoDeTisVa4zTgyTZqeLoXkaJ1//.U2',
 '97010005',
 4,
 'CV_David_SOSSOU.pdf',
 'actif',
 '2026-10-02 19:33:29'),

(6, 'BIEM', 'MOÏSE',
 'onbouge9@gmail.com',
 '$2y$10$0RR4YoBum9wpc3BJoVljieLx3jNo./1UnpEQBYRNqiiWG2a4m2CD.',
 '0199513488',
 1,
 'CV_BIEM_MOISE.pdf',
 'actif',
 '2026-10-03 10:46:52');


-- ============================================================
-- 12. ENTREPRISES
-- ============================================================

INSERT INTO `entreprise`
(`id`, `nom`, `email`, `telephone`, `adresse`,
 `id_secteur`, `id_ville`, `date_creation`)
VALUES
(1,
 'Tech Solutions Benin',
 'contact@techsolutions.bj',
 '97000001',
 'Cotonou',
 1,
 1,
 '2026-10-02 19:32:32'),

(2,
 'Finance Plus',
 'contact@financeplus.bj',
 '97000002',
 'Porto-Novo',
 2,
 2,
 '2026-10-02 19:32:32'),

(3,
 'Benin Commerce',
 'contact@benincommerce.bj',
 '97000003',
 'Abomey-Calavi',
 3,
 3,
 '2026-10-02 19:32:32'),

(4,
 'Digital Agency BJ',
 'contact@digitalagency.bj',
 '97000004',
 'Cotonou',
 4,
 1,
 '2026-10-02 19:32:32'),

(5,
 'HR Consulting Benin',
 'contact@hrconsulting.bj',
 '97000005',
 'Cotonou',
 5,
 1,
 '2026-10-02 19:32:32');


-- ============================================================
-- 13. OFFRES
-- ============================================================

INSERT INTO `offre`
(`id`, `titre`, `description`, `salaire`, `date_limite`,
 `statut`, `id_entreprise`, `id_secteur`, `id_ville`,
 `id_type_contrat`, `date_publication`)
VALUES

(1,
 'Développeur Web PHP',
 'Développement et maintenance d applications web en PHP et MySQL.',
 350000.00,
 '2026-10-30',
 'publiee',
 1,
 1,
 1,
 1,
 '2026-09-29 09:00:00'),

(2,
 'Développeur Front-End',
 'Création et amélioration des interfaces web avec HTML, CSS, JavaScript et Bootstrap.',
 300000.00,
 '2026-10-25',
 'publiee',
 1,
 1,
 2,
 1,
 '2026-09-29 09:00:00'),

(3,
 'Assistant Comptable',
 'Participation aux opérations comptables et au suivi administratif.',
 250000.00,
 '2026-10-20',
 'publiee',
 2,
 2,
 1,
 2,
 '2026-09-29 11:00:00'),

(4,
 'Stagiaire Finance',
 'Appui à l équipe financière dans le traitement et le classement des documents.',
 NULL,
 '2026-10-15',
 'publiee',
 2,
 2,
 2,
 3,
 '2026-09-29 12:00:00'),

(5,
 'Commercial',
 'Développement du portefeuille clients et présentation des produits de l entreprise.',
 200000.00,
 '2026-10-28',
 'publiee',
 3,
 3,
 3,
 2,
 '2026-09-30 09:00:00'),

(6,
 'Assistant Marketing',
 'Participation à la préparation et au suivi des campagnes marketing.',
 275000.00,
 '2026-10-22',
 'publiee',
 4,
 4,
 1,
 1,
 '2026-09-30 10:00:00'),

(7,
 'Community Manager',
 'Gestion des réseaux sociaux et création de contenus numériques.',
 250000.00,
 '2026-10-24',
 'publiee',
 4,
 4,
 1,
 2,
 '2026-09-30 11:00:00'),

(8,
 'Assistant Ressources Humaines',
 'Participation à la gestion administrative des ressources humaines.',
 300000.00,
 '2026-10-26',
 'publiee',
 5,
 5,
 1,
 1,
 '2026-09-30 12:00:00'),

(9,
 'Stagiaire RH',
 'Appui au service ressources humaines dans ses activités quotidiennes.',
 NULL,
 '2026-10-18',
 'publiee',
 5,
 5,
 1,
 3,
 '2026-10-01 09:00:00'),

(10,
 'Développeur Full Stack',
 'Développement de fonctionnalités front-end et back-end pour les applications de l entreprise.',
 450000.00,
 '2026-10-31',
 'publiee',
 1,
 1,
 1,
 1,
 '2026-10-01 10:00:00');


-- ============================================================
-- 14. CANDIDATURES
-- ============================================================

INSERT INTO `candidature`
(`id`, `id_candidat`, `id_offre`, `lettre_motivation`,
 `statut`, `date_candidature`)
VALUES

(1,
 1,
 1,
 'Je souhaite mettre mes compétences en développement PHP au service de votre entreprise.',
 'en_attente',
 '2026-10-02 19:34:31'),

(2,
 1,
 3,
 'Je suis particulièrement intéressé par cette opportunité dans le domaine de la finance.',
 'retenue',
 '2026-10-02 19:34:31'),

(3,
 2,
 6,
 'Ma formation et mon expérience en marketing correspondent aux exigences de cette offre.',
 'en_attente',
 '2026-10-02 19:34:31'),

(4,
 3,
 5,
 'Je souhaite rejoindre votre équipe commerciale et contribuer au développement de votre activité.',
 'refusee',
 '2026-10-02 19:34:31'),

(5,
 4,
 2,
 'Je souhaite mettre mes compétences front-end au service de vos projets numériques.',
 'en_attente',
 '2026-10-02 19:34:31'),

(6,
 5,
 8,
 'Je suis motivé à rejoindre votre équipe RH et à développer mes compétences dans ce domaine.',
 'en_attente',
 '2026-10-02 19:34:31'),

(9,
 6,
 10,
 'Je souhaite vous soumettre ma candidature pour ce poste. Mon parcours et mes compétences correspondent aux exigences de cette offre.',
 'refusee',
 '2026-10-03 11:11:19');