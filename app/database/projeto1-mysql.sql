
CREATE TABLE Pedido( 
      id  INT  AUTO_INCREMENT    NOT NULL  , 
      cliente varchar (200)   , 
      vendedor varchar (200)  , 
      valor varchar (50) , 
      status varchar (100)  
 PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 
