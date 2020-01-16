import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CadastroEspecialidadeComponent } from './cadastro-especialidade.component';

describe('CadastroEspecialidadeComponent', () => {
  let component: CadastroEspecialidadeComponent;
  let fixture: ComponentFixture<CadastroEspecialidadeComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CadastroEspecialidadeComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CadastroEspecialidadeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
