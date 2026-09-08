CREATE DATABASE IF NOT EXISTS entreprise;
USE entreprise;

CREATE TABLE IF NOT EXISTS departements (
    deptEmpl INT PRIMARY KEY,
    mgrDept INT NULL
);

CREATE TABLE IF NOT EXISTS employes (
    idEmpl INT PRIMARY KEY AUTO_INCREMENT,
    nomEmpl VARCHAR(100) NOT NULL,
    salaire DECIMAL(10,2) NOT NULL,
    deptEmpl INT NULL,

    CONSTRAINT fk_employe_departement
        FOREIGN KEY (deptEmpl)
        REFERENCES departements(deptEmpl)
        ON UPDATE CASCADE
        ON DELETE SET NULL
);

ALTER TABLE departements
ADD CONSTRAINT fk_mgr_dept
FOREIGN KEY (mgrDept)
REFERENCES employes(idEmpl)
ON UPDATE CASCADE
ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS projets (
    nomProj VARCHAR(100) PRIMARY KEY,
    budget DECIMAL(12,2),
    mgrProj INT NULL,
    dateDeut DATE,

    CONSTRAINT fk_projet_manager
        FOREIGN KEY (mgrProj)
        REFERENCES employes(idEmpl)
        ON UPDATE CASCADE
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS employe_projet (
    idEmpl INT,
    nomProj VARCHAR(100),
    heures INT,
    evalEmpl DECIMAL(4,2),

    PRIMARY KEY (idEmpl, nomProj),

    CONSTRAINT fk_ep_employe
        FOREIGN KEY (idEmpl)
        REFERENCES employes(idEmpl)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_ep_projet
        FOREIGN KEY (nomProj)
        REFERENCES projets(nomProj)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);