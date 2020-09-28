import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
    providedIn: 'root'
})
export class ConsultasService {

    url:string;
    constructor(
        private http: HttpClient,
    ) { 
        const host = localStorage.getItem("host");
        //this.url='http://localhost:8080/api/routes'
        this.url = `http://${host}/api/routes/`;
    }

    listaDeConsultas(): Observable<any[]> {
        return this.http.get<any[]>(this.url + "consulta/index.php")
    }
    lerConsulta(id) {
        return this.http.get(this.url + 'consulta/read.php', {
            headers: {
                '_id': String(id),
            }
        })
    }
    editarConsulta(form): Observable<any> {
        return this.http.post<any>(this.url + 'consulta/update.php', {
            '_id': form.codigo,
            'tipo_consulta': form.categoria,
            'status': form.status,
            'validade': form.validade,
            'inicio': form.inicio,
            'termino': form.termino,
            "dataHora":form.dataHora
        });
    }
    statusConsulta(dados): Observable<any> {
        return this.http.post<any>(this.url + 'consulta/change_status.php', {
            '_id': dados.consulta,
            'status': dados.status
        });
    }
    deletarConsulta(id): Observable<any> {
        return this.http.post<any>(this.url + 'consulta/delete.php', {
            "_id": String(id)
        })
    }
}
