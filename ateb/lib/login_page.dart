import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'home_page.dart';

class LoginPage extends StatefulWidget {
  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final usuarioController = TextEditingController();
  final senhaController = TextEditingController();
  bool carregando = false;

  Future<void> login() async {
    setState(() => carregando = true);
    final response = await http.post(
      Uri.parse("http://192.168.6.121/ateb/login.php"),
      body: {
        'usuario': usuarioController.text,
        'senha': senhaController.text,
      },
    );

    if (response.body.contains("cliente")) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => HomePage()),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Usuário ou senha inválidos")),
      );
    }

    setState(() => carregando = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Login')),
      body: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            TextField(controller: usuarioController, decoration: InputDecoration(labelText: 'Usuário')),
            TextField(controller: senhaController, obscureText: true, decoration: InputDecoration(labelText: 'Senha')),
            SizedBox(height: 20),
            carregando
                ? CircularProgressIndicator()
                : ElevatedButton(onPressed: login, child: Text('Entrar')),
          ],
        ),
      ),
    );
  }
}
