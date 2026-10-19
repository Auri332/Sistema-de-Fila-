import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

class SenhaPage extends StatefulWidget {
  final String servico, sigla;
  final int balcao;

  SenhaPage({required this.servico, required this.sigla, required this.balcao});

  @override
  State<SenhaPage> createState() => _SenhaPageState();
}

class _SenhaPageState extends State<SenhaPage> {
  String senha = '';

  @override
  void initState() {
    super.initState();
    gerarSenha();
  }

  Future<void> gerarSenha() async {
    final response = await http.post(
      Uri.parse('http://192.168.6.121/ateb/api_senha_chamada.php'),
      body: {
        'servico': widget.servico,
        'sigla': widget.sigla,
        'balcao': widget.balcao.toString(),
      },
    );

    if (response.statusCode == 200) {
      setState(() {
        senha = response.body.trim();
      });

      // TODO: Integrar impressão automática aqui se necessário
    } else {
      setState(() {
        senha = "Erro ao gerar senha.";
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Senha')),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Image.asset('assets/logo.png', height: 80),
            SizedBox(height: 20),
            Text(widget.servico, style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
            Text(senha, style: TextStyle(fontSize: 48)),
            Text('Balcão ${widget.balcao}', style: TextStyle(fontSize: 16)),
          ],
        ),
      ),
    );
  }
}
