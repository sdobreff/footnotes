let opt = {
    width: () => window.innerWidth - 300 < 100 ? window.innerWidth - 60 : 300,
    height: 150,
    ref: href => {
        let hash = new URL(href).hash
        let ref = document.querySelector(hash)
        return ref?.innerHTML || 'invalid ref'
    },
    before_hook: null,
    after_hook: null
}

let id = {
    wrapper: "footnotes_dialog_12c1b560",
    get content() { return this.wrapper + '_content' }
}

export default function(css_selector, options) {
    Object.assign(opt, options)
    document.querySelectorAll(css_selector).forEach( node => {
       // node.addEventListener('mouseover', is_mobile() ? dialog_create : debounce(dialog_create))
        node.addEventListener('mouseover', dialog_create)
       // node.addEventListener('mouseout', dialog_remove)
        node.addEventListener('click', e => e.preventDefault())
    })
}

function dialog_create(event) {
    // don't show the dialog if a user just quickly moved a mouse
    // cursor over a link
    if (!event.target.matches(':hover')) return

    // console.log(event.currentTarget.getBoundingClientRect())
    // console.log(event.currentTarget.getBoundingClientRect()['left'] + event.currentTarget.offsetWidth/2)
    // console.log(event.x)

    dialog_remove2(event)

    let width = opt.width()
    let padding = 16
    let border = 1
    let scrollbar = 16 // a guess
    let width_total = width + padding*2 + border*2 + scrollbar
    let height = opt.height
    let transparent_margin = 3
    let height_total = height + padding*2 + border*2 + transparent_margin*2
    let x = (event.currentTarget.getBoundingClientRect()['left'] + event.currentTarget.offsetWidth/2) - width/2
    let y = event.currentTarget.getBoundingClientRect()['bottom']

    let correction = 0;

    // this div is transparent
    let wrapper = document.createElement('div')
    wrapper.id = id.wrapper
    wrapper.style.position = 'fixed'
    wrapper.style.left = `${x}px`
    wrapper.style.top =`${(y+transparent_margin+10)}px`
//    wrapper.style.outline = '1px solid red'

    /* wrapper inner div is not transparent

       +-------------+
       | transparent |
       |+-----------+|
       ||           ||
       ||   dlg     ||
       ||           ||
       |+-----------+|
       | transparent |
       +-------------+
    */
    wrapper.innerHTML = `<div id="${id.content}"></div>`
    let dlg = wrapper.querySelector('#' +id.content)
    dlg.style.marginTop = transparent_margin + 'px'
    dlg.style.marginBottom = transparent_margin + 'px'
    wrapper.style.border = `1px solid lightgray`
    wrapper.style.boxShadow = '0 0 10px #ccc'
    wrapper.style.borderRadius = '5px'
    wrapper.style.background = '#fffaf0'
    //dlg.style.background = 'white'
    dlg.style.color = '#191919'
    dlg.style.padding = padding + 'px'
    dlg.style.overflowY = 'auto'
    dlg.style.width = width + 'px'
    dlg.style.height = height + 'px'

    // fix coordinates if a mouse cursor is too close to the viewport edges
    if (x < 0) {
        wrapper.style.left = '0px';
        correction = x;
    } 
    if (window.innerWidth - x < width_total) {
        correction = x - (window.innerWidth - width_total);
        wrapper.style.left = (window.innerWidth - width_total) + 'px'
    }
    if (window.innerHeight - y < height_total) {
        // first, try position the popup above the link
        wrapper.style.top = (y - height_total) + 'px'
        // second, if there is no space above, stick it to the bottom
        if (y - height_total < 0)
            wrapper.style.top = (window.innerHeight - height_total) + 'px'
    }

    dlg.innerHTML = opt.ref(this.href)
    dlg.innerHTML += '<style>#'+id.wrapper+':before {' +
        'content: "";' +
        'position: absolute;' +
        'top: 1px;' +
        'right: '+(width/2 - correction)+'px;' +
        'width: 20px;' +
        'height: 20px;' +
        'background-color: #fffaf0;' +
        'box-shadow: 0 0 10px #ccc;' +
        'transform: translate(50%, -50%) rotate(-45deg);' +
        'clip-path: polygon(' +
          'calc(10px * -1) calc(10px * -1), ' +
          'calc(100% + 10px) calc(10px * -1), ' +
          'calc(100% + 10px) calc(100% + 10px)' +
        ');' +
      '}</style>';

   wrapper.addEventListener('mouseout', dialog_remove2)

    if (opt.before_hook) opt.before_hook(event.target)
    document.querySelector('body').appendChild(wrapper)
    document.addEventListener('scroll', dialog_remove2)
}

function dialog_remove(event) {
    let dlg = document.querySelector('#' + id.wrapper)
    if (!dlg || dlg.matches(':hover')) return

    document.querySelectorAll('#' + id.wrapper).forEach( div => {
        if (dlg === event?.relatedTarget) return
        div.remove()
    })
    if (opt.after_hook) opt.after_hook(event.target)
}

function dialog_remove2(event) {
    let removed = false
    document.querySelectorAll('#' + id.wrapper).forEach( div => {
        if (event?.relatedTarget?.closest('#' + id.content)) {
            // do nothing: mouseout event was fired because a cursor
            // entered a child element (<a> for example) withing the
            // dialog
        } else {
            div.classList.add('animate');
            addEventListener("transitionend", (event) => {
                div.remove();
                removed = true
            });
        }
    })
    if (removed && opt.after_hook) opt.after_hook(event.target)
}

function debounce(fn, ms = 250) {
    let timeout_id
    return function(...args) {
        clearTimeout(timeout_id)
        timeout_id = setTimeout(() => fn.apply(this, args), ms)
    }
}

function is_mobile() {
    return /iPhone|iPad|iPod|Android/i.test(navigator.userAgent)
}
