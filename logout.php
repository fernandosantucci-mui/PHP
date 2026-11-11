<?php
	// abre sess�o
	session_start();

	// apaga sess�o
	session_destroy();

	// volta para a tela de login
	header("Location: index_2.html");
?>