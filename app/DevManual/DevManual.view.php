<H2>Регистрация нового модуля 1го уровня</H2>
<ol>
    <li>Создать папку с именем модуля (например, <code>ModuleName</code>) в <code>root/app/</code>. Файлы модуля размещать в ней:</li>
    <ul>
        <li><code>ModuleName.view.php</code> - <strong>обязательный</strong> компонент модуля - пользовательское представление,
            доступное по навигации основного меню приложения</li>
        <li><code>ModuleName.onSubmit.php</code> - компонента обработки данных пользовательских форм</li>
        <li>Файлы с классами должны заканчиваться на <code>.class.php</code>. Например, <code>ModuleName.class.php</code></li>
    </ul>
    <li>Зарегистрировать модуль в <code>root/app/config.modules.php</code> в аргументах-массивах
        вызова <code>MODULE::initialize()</code>:</li>
    <ul>
        <li>в 1ом: добавить пару <code>'url' => 'ModuleName',</code>. Порядок определяет очерёдность пунктов главного навигационного меню</li>
        <li>во 2ом: добавить настройки модуля по образцу других модулей - название пункта меню,
            заголовок пользовательского представления,
            <code>'onSubmit'  =>  true,</code>, если модуль имеет соответствующую компоненту</li>
    </ul>
</ol>