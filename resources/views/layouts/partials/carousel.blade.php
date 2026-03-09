 <div id="portalCarousel" class="carousel slide pointer-event" data-bs-ride="carousel">

     <div class="carousel-indicators">
         <button type="button" data-bs-target="#portalCarousel" data-bs-slide-to="0" class="active" aria-current="true"
             aria-label="Slide 1"></button>
         <button type="button" data-bs-target="#portalCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
     </div>

     <div class="carousel-inner">

         <div class="carousel-item active"
             style="background: url('{{ asset('img/bg1.jpg') }}') no-repeat center center; background-size: cover; padding: 60px 0;">
             <div class="container">
                 <div class="row align-items-center">
                     <div class="col-md-7 text-secondary animate__animated animate__fadeInLeft">
                         <div class="d-flex align-items-center mb-4">

                             <h2 class="fw-light mb-4"
                                 style="font-size: 2.2rem; color: #4b647c; text-transform: uppercase;">CARO CIDADÃO
                             </h2>
                         </div>
                         <p class="text-start">A(O) Prefeitura Municipal De Cacequi apresenta o seu mais novo PORTAL
                             DE
                             TRANSPARÊNCIA, em atendimento à Lei Complementar 131/2009 de 27 de maio de 2009.</p>
                         <p class="text-start">Por este canal o cidadão poderá acompanhar a aplicação dos recursos
                             públicos,
                             compreendendo a arrecadação de recursos próprios e recebidos em transferências, a
                             execução das
                             despesas, inclusive os contratos, convênios e instrumentos congêneres celebrados pelos
                             órgãos e
                             entidades, sendo oferecidas informações detalhadas quanto ao número do correspondente
                             processo,
                             ao bem fornecido ou ao serviço prestado, à pessoa física ou jurídica beneficiária do
                             pagamento
                             e, quando for o caso, ao procedimento licitatório realizado, dentre outras.</p>
                         <p class="text-start">O Portal da transparência constitui um mecanismo de controle social
                             pioneiro.
                             A partir deste instrumento, independente de senha e que pode ser acessado através da
                             rede
                             mundial de computadores (internet), espera-se melhorar o canal de comunicação entre a
                             sociedade
                             e as atividades realizadas pelo governo.</p>
                     </div>
                     <div class="col-md-5 text-center animate__animated animate__fadeInRight">
                         <img src="{{ asset('img/brasil-lupa.png') }}" class="img-fluid" style="max-height: 350px;">
                     </div>
                 </div>
             </div>
         </div>

         <div class="carousel-item"
             style="background: url('{{ asset('img/bg1.jpg') }}') no-repeat center center; background-size: cover; padding: 60px 0;">
             <div class="container">
                 <div class="row align-items-center">
                     <div class="col-md-7 text-secondary animate__animated animate__fadeInUp">
                         <h2 class="fw-light mb-4"
                             style="font-size: 2.2rem; color: #4b647c; text-transform: uppercase;">Consulta aos
                             Gastos e Receitas Públicas</h2>
                         <p>São os gastos que a união, estados, municípios e outros órgãos públicos fazem
                             diretamente, na contratação de obras, na compra de bens, na realização de serviços e no
                             pagamento de pessoal, entre outros.</p>
                         <p>São os recursos financeiros que a união, estados, municípios e outros órgãos públicos
                             arrecadam, em sua maioria sob a forma de impostos, para atender os gastos com serviços,
                             obras, compras e salários dos servidores.</p>
                     </div>
                     <div class="col-md-5 text-center animate__animated animate__zoomIn">
                         <img src="{{ asset('img/lupa-gastos.png') }}" class="img-fluid" style="max-height: 350px;">
                     </div>
                 </div>
             </div>
         </div>

     </div>

     <button class="carousel-control-prev" type="button" data-bs-target="#portalCarousel" data-bs-slide="prev">
         <span class="carousel-control-prev-icon" aria-hidden="true"
             style="background-color: rgba(0,0,0,0.3); border-radius: 50%; padding: 20px;"></span>
     </button>
     <button class="carousel-control-next" type="button" data-bs-target="#portalCarousel" data-bs-slide="next">
         <span class="carousel-control-next-icon" aria-hidden="true"
             style="background-color: rgba(0,0,0,0.3); border-radius: 50%; padding: 20px;"></span>
     </button>
 </div>
