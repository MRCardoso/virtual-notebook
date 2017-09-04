<div class="center">
    <?php if(auth() != NULL): ?>
        <div class="centralized">
            <h3>Bem-Vindo!</h3>
        </div>
    <?php else: ?>
        <div class="box-item">
            <div class="box-text">
                <h3 class="title">Agenda Virtual</h3>
                <p>
                    Salve seus contatos, em sua agenda online,
                    crie sua lista de contatos, com telefone e e-mail, <br>
                    controlando organizando e acessando rápidamente seus dados.
                </p>
            </div>
            
            <a href="<?php echo $this->route('notebook'); ?>">
                <img src="<?php echo $this->baseUrl('public/images/contact.png')?>" alt="Agenda">
            </a>
        </div>
    <?php endif; ?>
</div>