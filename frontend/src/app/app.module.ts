import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { LoginComponent } from './login/login.component';
import { FormsModule } from '@angular/forms';
import { HttpClientModule  } from '@angular/common/http';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './auth/auth.guard';
import { ClasseComponent } from './classe/classe.component'; // Assurez-vous que le chemin est correct


const routes: Routes = [
  { path: 'login', component: LoginComponent },
 

];


@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    ClasseComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    FormsModule,
    HttpClientModule,
    RouterModule.forRoot(routes)
  ],
  providers: [AuthGuard, ],
  bootstrap: [AppComponent]
})
export class AppModule { }
