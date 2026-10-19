import 'package:flutter/material.dart';
import 'senha_page.dart';
import 'login_page.dart';

class HomePage extends StatelessWidget {
  void gerarSenha(BuildContext context, String servico, String sigla, int balcao) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => SenhaPage(servico: servico, sigla: sigla, balcao: balcao),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Sistema de Atendimento')),
      body: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            Image.asset('assets/logo.png', height: 100),
            SizedBox(height: 20),
            Text('Bem-vindo ao Hospital Central do Lubango', style: TextStyle(fontSize: 18), textAlign: TextAlign.center),
            SizedBox(height: 30),
            ElevatedButton(
              onPressed: () => gerarSenha(context, "Atendimento Geral", "G", 1),
              child: Text('Atendimento Geral'),
            ),
            ElevatedButton(
              onPressed: () => gerarSenha(context, "Atendimento de Prioridade", "P", 1),
              child: Text('Atendimento de Prioridade'),
            ),
            ElevatedButton(
              onPressed: () => gerarSenha(context, "Isenção", "I", 4),
              child: Text('Atendimento de Isenção'),
            ),
            Spacer(),
            OutlinedButton(
              onPressed: () => Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => LoginPage())),
              child: Text("Sair"),
            ),
          ],
        ),
      ),
    );
  }
}
