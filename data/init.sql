CREATE DATABASE ehrdb;

use ehrdb;

CREATE TABLE users
(
id VARCHAR(255) NOT NULL, 
first_name VARCHAR(255) NOT NULL,
last_name VARCHAR(255),
middle_name VARCHAR(255) NOT NULL,
email VARCHAR(255) NOT NULL,
username VARCHAR(255) NOT NULL,
password VARCHAR(255) NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id),
CONSTRAINT UNIQUE (username)
);

CREATE TABLE provider_type
(
id VARCHAR(255) NOT NULL,
name VARCHAR(255) NOT NULL,
max_patients INT NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id)
);

CREATE TABLE providers
(
id VARCHAR(255) NOT NULL,
user_id VARCHAR(255) NOT NULL,
provider_type_id VARCHAR(255) NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id),
CONSTRAINT providers FOREIGN KEY (user_id) REFERENCES users (id),
CONSTRAINT provider_type FOREIGN KEY (provider_type_id) REFERENCES provider_type (id)
);


CREATE TABLE patients
(
id VARCHAR(255) NOT NULL,
user_id VARCHAR(255) NOT NULL,
date_of_birth DATE NOT NULL,
date_of_death DATE,
sex ENUM('Male', 'Female', 'Intersex')  NOT NULL,
phone VARCHAR(255),
address_street VARCHAR(255),
address_city VARCHAR(255),
address_state VARCHAR(255),
address_zip VARCHAR(255),
marriage_status ENUM('Single', 'Married', 'Divorced', 'Widowed') NOT NULL,
children_count INT NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id),
CONSTRAINT patients FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE caretakers
(
id VARCHAR(255) NOT NULL,
user_id VARCHAR(255) NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id),
CONSTRAINT caretakers FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE caretaker_patient
(
patient_id VARCHAR(255) NOT NULL,
caretaker_id VARCHAR(255) NOT NULL,
status ENUM('Active', 'Inactive') NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
CONSTRAINT patient_caretaker FOREIGN KEY (patient_id) REFERENCES patients (id),
CONSTRAINT caretaker_patient FOREIGN KEY (caretaker_id) REFERENCES caretakers (id)
);


CREATE TABLE provider_patient
(
patient_id VARCHAR(255) NOT NULL,
provider_id VARCHAR(255) NOT NULL,
status ENUM('Active', 'Inactive', 'Inactive-Requested') NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
CONSTRAINT patient_provider FOREIGN KEY (patient_id) REFERENCES patients (id),
CONSTRAINT provider_patient FOREIGN KEY (provider_id) REFERENCES providers (id)
);

CREATE TABLE locations
(
id VARCHAR(255) NOT NULL,
name VARCHAR(255) NOT NULL,
phone VARCHAR(255) NOT NULL,
address_street VARCHAR(255) NOT NULL,
address_city VARCHAR(255) NOT NULL,
address_state VARCHAR(255) NOT NULL,
address_zip VARCHAR(255) NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id)
);

CREATE TABLE visits
(
id VARCHAR(255) NOT NULL,
patient_id VARCHAR(255) NOT NULL,
provider_id VARCHAR(255) NOT NULL,
location_id VARCHAR(255) NOT NULL,
type ENUM('Outpatient', 'Virtual Visit', 'Urgent Care', 'Emergency Room', 'Inpatient') NOT NULL,
symptoms TEXT,
start_date DATE NOT NULL,
end_date DATE,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id),
CONSTRAINT visit_patient FOREIGN KEY (patient_id) REFERENCES patients (id),
CONSTRAINT visit_provider FOREIGN KEY (provider_id) REFERENCES providers (id),
CONSTRAINT visit_location FOREIGN KEY (location_id) REFERENCES locations (id)
);

CREATE TABLE observations
(
id VARCHAR(255) NOT NULL,
visit_id VARCHAR(255) NOT NULL,
type VARCHAR(255) NOT NULL,
data VARCHAR(255) NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id),
CONSTRAINT observations_visit FOREIGN KEY (visit_id) REFERENCES visits (id)
);

CREATE TABLE conditions
(
id VARCHAR(255) NOT NULL,
name VARCHAR(255) NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id)
);

CREATE TABLE diagnoses
(
id VARCHAR(255) NOT NULL,
condition_id VARCHAR(255) NOT NULL,
patient_id VARCHAR(255) NOT NULL,
provider_id VARCHAR(255) NOT NULL,
status ENUM('Chronic Active', 'Chronic Inactive', 'Acute') NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id),
CONSTRAINT diagnosis_condition FOREIGN KEY (condition_id) REFERENCES conditions (id),
CONSTRAINT diagnosis_patient FOREIGN KEY (patient_id) REFERENCES patients (id),
CONSTRAINT diagnosis_provider FOREIGN KEY (provider_id) REFERENCES providers (id)
);

CREATE TABLE operations
(
id VARCHAR(255) NOT NULL,
visit_id VARCHAR(255) NOT NULL,
type VARCHAR(255) NOT NULL,
notes VARCHAR(255) NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id),
CONSTRAINT operation_visit FOREIGN KEY (visit_id) REFERENCES visits (id)
);

CREATE TABLE medications
(
id VARCHAR(255) NOT NULL,
name VARCHAR(255) NOT NULL,
manufacturer VARCHAR(255) NOT NULL,
shelf_life INT NOT NULL,
created_at date NOT NULL,
updated_at date NOT NULL,
PRIMARY KEY (id)
);

CREATE TABLE lab_tests
(
Id VARCHAR(255) NOT NULL,
test_name VARCHAR(255) NOT NULL,
manufacturer VARCHAR(255) NOT NULL,
laboratory VARCHAR(255) NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id)
);

CREATE TABLE lab_test_orders
(
id VARCHAR(255) NOT NULL,
patient_id VARCHAR(255) NOT NULL,
provider_id VARCHAR(255) NOT NULL,
lab_test_id VARCHAR(255) NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id),
CONSTRAINT lab_order_patient FOREIGN KEY (patient_id) REFERENCES patients (id),
CONSTRAINT lab_order_provider FOREIGN KEY (provider_id) REFERENCES providers (id),
CONSTRAINT lab_order_test FOREIGN KEY (lab_test_id) REFERENCES lab_tests (id)
);

CREATE TABLE lab_test_results
(
id VARCHAR(255) NOT NULL,
test_order_id VARCHAR(255) NOT NULL,
results VARCHAR(255) NOT NULL,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id),
CONSTRAINT lab_result_order FOREIGN KEY (test_order_id) REFERENCES lab_test_orders (id)
);


CREATE TABLE medication_orders
(
id VARCHAR(255) NOT NULL,
patient_id VARCHAR(255) NOT NULL,
provider_id VARCHAR(255) NOT NULL,
medication_id VARCHAR(255) NOT NULL,
picked_up_at DATE,
created_at DATE NOT NULL,
updated_at DATE NOT NULL,
PRIMARY KEY (id),
CONSTRAINT medication_order_patient FOREIGN KEY (patient_id) REFERENCES patients (id),
CONSTRAINT medication_order_provider FOREIGN KEY (provider_id) REFERENCES providers (id),
CONSTRAINT medication_order_medication FOREIGN KEY (medication_id) REFERENCES medications (id)
);

CREATE TABLE visit_media
(
id VARCHAR(255) NOT NULL,
meta_data VARCHAR(255),
data BLOB NOT NULL,
visit_id VARCHAR (255) NOT NULL,
PRIMARY KEY (id),
CONSTRAINT visit_media_id FOREIGN KEY (visit_id) REFERENCES visits (id)
);


CREATE TRIGGER no_self_care BEFORE INSERT ON visits
	FOR EACH ROW
	BEGIN
		IF new.patient_id IN (
			SELECT patients.id 
			FROM patients 
			JOIN users ON users.id = patients.user_id
			JOIN providers ON providers.user_id = users.id
			WHERE providers.id = new.provider_id)
    	THEN SIGNAL SQLSTATE VALUE '45000' SET MESSAGE_TEXT = 'INSERT failed due to provider trying to treat themself';
	END IF;
END;


DELIMITER $$
CREATE FUNCTION check_max_patients(new_provider_id VARCHAR(255))
	RETURNS BOOLEAN
	BEGIN
		DECLARE accepting BOOLEAN;
		SET accepting = (SELECT
		count(*) < provider_type.max_patients
		FROM 
		provider_patient
		JOIN providers ON providers.id = provider_patient.provider_id
		JOIN provider_type ON provider_type.id = providers.provider_type_id
		WHERE provider_id = new_provider_id
		GROUP BY provider_id);
		RETURN accepting;
	END$$
DELIMITER ;

INSERT INTO conditions VALUES ("1", "Acne", "2019-05-10","2019-05-10");
INSERT INTO conditions VALUES ("2", "Migraines", "2019-05-10","2019-05-10");
INSERT INTO conditions VALUES ("3", "Strep Throat", "2019-05-10","2019-05-10");
INSERT INTO conditions VALUES ("4", "UTI", "2019-05-10","2019-05-10");
INSERT INTO conditions VALUES ("5", "Sprained Ankle", "2019-05-10","2019-05-10");
INSERT INTO lab_test VALUES ("1", "Vitamin D Test", "D Corp", "D Lab", "2019-05-10", "2019-05-10")
INSERT INTO locations VALUES("1", "Hospital X",	"555-555-5555",	"101 Main Street", "Townville",	"CA","95000","2019-01-01","2019-01-01")
INSERT INTO medications VALUES ("1", "Dermatrex", "DermCo", 5, "2019-05-10", "2019-05-10" )
INSERT INTO provider_type VALUES ("1", "Primary Care", 50, "2019-05-10", "2019-05-10")

