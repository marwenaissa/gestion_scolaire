import { Component, OnInit } from '@angular/core';
import { LoginService } from '../services/login.service';
import { Router } from '@angular/router'; // Importer le service Router
import { HttpClient } from '@angular/common/http';
import { Observable , BehaviorSubject } from 'rxjs';


@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  email: string = '';
  password: string = '';

  
  constructor(private LoginService: LoginService, private http: HttpClient, private router: Router) {}
  user = { username: '', password: '' };
  ngOnInit(): void {
    // Vérifiez si l'utilisateur est déjà connecté
    if (this.LoginService.isLoggedIn()) {
      // Redirigez l'utilisateur vers la page d'accueil s'il est déjà connecté
      this.router.navigate(['/produit']);
    }
  }

  
  onSubmit() {
    this.LoginService.login(this.email, this.password)
      .subscribe(
        (response: { token: string }) => {  // Définissez le type de 'response'
          console.log('Réponse du serveur:', response);
  

          const token = response.token;
          this.LoginService.setToken(token);


        console.log("sa passe par onsubbmit");
          

          this.LoginService.handleLoginResponse(response);

          // Supposons que le serveur renvoie un token
          if (response && response.token) {
            // Stockez le token JWT dans votre application si nécessaire
            // (par exemple, utilisez un service d'authentification)
            // this.authService.setToken(response.token);
  
            // Redirection vers la route "produit"
            this.router.navigate(['/produit']);
          }
        },
        (error: any) => {
          console.error('Erreur lors de la connexion:', error);
        }
      );

      
  }

  logout() {
    this.LoginService.logout();
  }

}
