import 'package:flutter/material.dart';
import 'login_page.dart';

void main() {
  runApp(SistemaAtendimentoApp());
}

class SistemaAtendimentoApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Sistema de Atendimento',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(primarySwatch: Colors.blue),
      home: LoginPage(),
    );
  }
}
