export let menuItems = [
    {id: 1, label: 'Детали записи', isActive: true, show: true},
    {id: 2, label: 'Уведомления', isActive: false, show: true},
    {id: 3, label: 'История изменений', isActive: false, show: false},
    {id: 4, label: 'Списание расходников', isActive: false, show: false},
    {id: 5, label: 'Статус визита', isActive: false, show: false},
    {id: 6, label: 'Оплата визита', isActive: false, show: false},
    {id: 7, label: 'Повторение', isActive: false, show: false},
    {id: 8, label: 'Клиент', isActive: false, show: false},
    {id: 9, label: 'Данные клиента', isActive: false, show: false},
    {id: 11, label: 'История посещений', isActive: false, show: false},
    {id: 12, label: 'Статистика', isActive: false, show: false},
    {id: 13, label: 'Отправленные сообщения', isActive: false, show: false},
    {id: 14, label: 'Отправить сообщение', isActive: false, show: false},
    {id: 15, label: 'Электронная карта', isActive: false, show: false},
    {id: 16, label: 'Лояльность', isActive: false, show: false},
    {id: 17, label: 'Счета клиента', isActive: false, show: false},
    {id: 18, label: 'История звонков', isActive: false, show: false},
    {id: 19, label: 'Файлы', isActive: false, show: false},
];

export let sheduleStatus = [
    {id: 1, label: 'Подтвердил'},
    {id: 2, label: 'Не подтвердил'},
    {id: 3, label: 'Пришёл'},
    {id: 4, label: 'Не пришёл'},
];

export let paymentTypes = [
    {id: 1, label: 'Оплата картой', icon: 'fas fa-credit-card'},
    {id: 2, label: 'Оплата наличными', icon: 'far fa-money-bill-alt'},
    {id: 3, label: 'Комбинированная', icon: ''},
]

export let sheduleObject = Object.assign({}, {
    id: null,
    client: {
        id: null,
        phone: null,
        name: null,
        status: false,
    },
    resourceId: null,
    start: null,
    end: null,
    operations: [],
    //Тип оплаты
    paymentType: false,
    //Сколько оплачено наличкой(сумма)
    paymentCash: null,
    //Оплачено картой (сумма)
    paymentCard: null,
    // Статус события
    status: false,
    //Чаевые
    tip: null,
});