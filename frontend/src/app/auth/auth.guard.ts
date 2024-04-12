import { Injectable } from '@angular/core';
import { LoginService } from '../services/login.service';

import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {
  constructor(private loginService: LoginService, private router: Router) {}

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
    if (this.loginService.isLoggedIn()) {
      console.log('2');
      
      //this.router.navigate(['/produit']);
      if (state.url === '/login') {
        //this.router.navigate(['/login']); // Redirige vers /produit si /login est demandé
      }
      return true;
      return true; // L'utilisateur est connecté, autoriser la navigation
    } else {
      console.log('1');
    //this.router.navigate(['/login']); 
      // L'utilisateur n'est pas connecté, rediriger vers la page de connexion
      return false;
    }
  }
}
