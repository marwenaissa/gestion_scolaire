import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable , BehaviorSubject } from 'rxjs';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class LoginService {
  private tokenKey = 'jwt_token'; 
  isLoggedIn(): boolean {
    const token = localStorage.getItem('jwt_token');
    return !!token; // Par exemple, retourne true si le jeton est présent, sinon false
  }
  getToken(): string | null {
    return localStorage.getItem(this.tokenKey);
  }
  setToken(token: string): void {
    localStorage.setItem(this.tokenKey, token);
  }
  removeToken(): void {
    localStorage.removeItem(this.tokenKey);
  }
  constructor(private http: HttpClient , private router: Router) {}
  login(email: string, password: string): Observable<any> {
    const user = { email, password }; // Créez un objet user avec les données fournies
    return this.http.post('http://localhost:8000/login', user);
  }

  handleLoginResponse(response: any): void {
    const token = response.token;
    console.log("sa passe par handleloginresponse");
    this.setToken(token);
    //this.router.navigate(['/produit']);
  }
  logout(): void {
    this.removeToken();
    //this.router.navigate(['/login']);
  }
}
