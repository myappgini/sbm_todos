CREATE TABLE `landini_todo` (
  `memberID` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `todos` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`memberID`)
) ENGINE = InnoDB;