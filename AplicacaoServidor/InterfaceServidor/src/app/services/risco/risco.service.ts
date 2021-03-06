import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { risco } from './risco';

@Injectable({
  providedIn: 'root'
})
export class RiscoService {

  url:string;

  constructor(
    private http:HttpClient,
  ) { 
    const host = localStorage.getItem("host");
    //this.url='http://localhost:8080/api/routes'
    this.url = `http://${host}/api/routes`;
  }

  listaDeRiscos():Observable<risco[]>{
    return this.http.get<risco[]>(this.url+'/risco/index.php')
  }
  deletarRisco(id):Observable<risco>{
    return this.http.post<risco>(this.url+'/risco/delete.php',{
      '_id':String(id)
    })
  }
  cadastrarRisco(dados){
    return this.http.post(this.url+"/risco/new.php",{
      "nome":dados.nome,
      "descricao":dados.descricao,
      "categoria":dados.codCategoria,
    })
  }
  editarRisco(dados){
    return this.http.post<risco[]>(this.url+"/risco/update.php", {
			"_id" : dados.codigo,
			"nome" : dados.nome,
      "descricao" : dados.descricao,
      "categoria":dados.codCategoriaRisco
		});
  }
  lerRisco(id){
    return this.http.get(this.url+"/risco/read.php", {
			headers : {

				'_id':String(id)
			}
		});
  }

}
