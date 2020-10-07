CREATE DATABASE dbapocalipse_zumbi;

USE dbapocalipse_zumbi;

CREATE TABLE tbsobreviventes(
	id INT PRIMARY KEY AUTO_INCREMENT,
	nome VARCHAR(255),
	sexo VARCHAR(1),
	idade INT,
	latitude DOUBLE,
	longitude DOUBLE,
	zumbi BOOLEAN
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE tbrecursos(
	id INT PRIMARY KEY,
	descricao VARCHAR(100),
	pontos INT
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

INSERT INTO tbrecursos (id, descricao, pontos)
VALUES
(1 ,'Água', 4),
(2, 'Comida', 3),
(3, 'Medicamento', 2),
(4, 'Munição', 1);

CREATE TABLE tbsobreviventes_recursos(
	id_sobrevivente INT,
	id_recurso INT,
	quantidade INT,
	CONSTRAINT PRIMARY KEY (id_sobrevivente, id_recurso),
	CONSTRAINT fk_sobrevivente00 FOREIGN KEY (id_sobrevivente) REFERENCES tbsobreviventes(id),
	CONSTRAINT fk_recurso00 FOREIGN KEY (id_recurso) REFERENCES tbrecursos(id)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE tbavisos_zumbificacao(
	id_sobrevivente INT,
	id_informante INT,
	CONSTRAINT fk_sobrevivente01 FOREIGN KEY (id_sobrevivente) REFERENCES tbsobreviventes(id),
	CONSTRAINT fk_sobrevivente02 FOREIGN KEY (id_informante) REFERENCES tbsobreviventes(id)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;