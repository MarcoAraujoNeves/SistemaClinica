import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { Observable } from "rxjs";
import { estados } from "./estado";

@Injectable({
    providedIn: "root"
})
export class EstadosService {
    //url = "http://localhost/SistemaClinica/AplicacaoServidor/api/routes/estado";
    url :string

    constructor(private http: HttpClient) { 
        const host = localStorage.getItem("host");
        this.url = `http://${host}/api/routes/estado`;
        //this.url = "/api/routes/estado"
    }

    agendarEmConsulta(consulta: number) {
        return this.http.post(`${this.url}/new.php`, {
            tipo: 1,
            consulta: consulta,
            termino: null,
        })
    }

    listaDeEstados(): Observable<estados[]> {
        return this.http.get<estados[]>(this.url);
    }

    criaEmEspera(consulta: number) {
        return this.http.post(`${this.url}/new.php`,
            {
                tipo: 3,
                termino: null,
                consulta,
            }
        );
    }

    criaAtrasado(consulta: number) {
        return this.http.post(`${this.url}/new.php`,
            {
                tipo: 4,
                termino: null,
                consulta,
            }
        );
    }

    criaCancelado(consulta: number) {
        return this.http.post(`${this.url}/new.php`,
            {
                tipo: 2,
                termino: null,
                consulta,
            }
        );
    }

    criaEncerrado(consulta: number) {
        return this.http.post(`${this.url}/new.php`,
            {
                tipo: 6,
                termino: null,
                consulta,
            }
        );
    }

    encerraEstado(id: number) {
        return this.http.post(`${this.url}/update.php`, { _id: id });
    }
    /*lerEstado(id) {
        return this.http.get(this.url + "/read.php", {
            headers: {
                db_user: "servidorLabmed",
                db_password: "labmed2019",
                _id: String(id)
            }
        });
    }

    cadastrarEstado(dados) {
        return this.http.post(
            this.url + "/new.php",
            {
                codConsulta: dados.codConsulta,
                paciente: dados.paciente,
                dataHora: dados.dataHora,
                empresa: dados.empresa,
                codTipoConsulta: dados.codTipoConsulta,
                tipo_consulta: dados.tipo_consulta,
                inicio: dados.inicio,
                termino: dados.termino,
                codEstado: dados.codEstado,
            },
            {
                headers: {
                    db_user: "servidorLabmed",
                    db_password: "labmed2019"
                }
            }
        );
    }

    atualizarEstado(dados): Observable<estados[]> {
        return this.http.post<estados[]>(
            this.url + "/update.php",
            {
                codConsulta: dados.codConsulta,
                paciente: dados.paciente,
                dataHora: dados.dataHora,
                empresa: dados.empresa,
                codTipoConsulta: dados.codTipoConsulta,
                tipo_consulta: dados.tipo_consulta,
                inicio: dados.inicio,
                termino: dados.termino,
                codEstado: dados.codEstado,
            },
            {
                headers: {
                    db_user: "servidorLabmed",
                    db_password: "labmed2019"
                }
            }
        );
    }

    deletarEstado(id): Observable<estados[]> {
        return this.http.post<estados[]>(
            this.url + "/delete.php",
            {
                _id: String(id)
            },
            {
                headers: {
                    db_user: "servidorLabmed",
                    db_password: "labmed2019"
                }
            }
        );
    }*/
}
