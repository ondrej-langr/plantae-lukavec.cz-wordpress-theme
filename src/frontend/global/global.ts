import aos from 'aos'
import simpleParalax from 'simple-parallax-js/vanilla'

import './index.css'
const $ = jQuery

document.addEventListener('DOMContentLoaded', function () {
    // inits start
    const image = document.getElementsByClassName('parallaxed_img')
    new simpleParalax(image, {
        delay: 0.9,
        transition: 'cubic-bezier(0,0,0,2)',
        scale: 1.12,
        maxTransition: 70,
    })

    aos.init()

    const body = $('body')
    let header: JQuery<HTMLElement>
    let headerMenu = $('header[scope="site"]')

    // inits start
    if (body.hasClass('front-page')) {
        header = $('.front-page .preview')
    } else if (body.hasClass('post')) {
        header = $('.post-header .parallaxed')
    } else {
        header = $('#hinfo')
    }

    $(window).scroll(function (event) {
        var scroll = $(window).scrollTop() ?? 0
        const offsetTop = header.offset()?.top ?? 0

        if (scroll > offsetTop + (header?.outerHeight(true) ?? 0) - 150) {
            if (!body.hasClass('header-on')) {
                body.addClass('header-on')
                headerMenu
                    .removeClass('fadeOut')
                    .addClass('animated')
                    .addClass('fadeIn')
            }
        } else {
            if (body.hasClass('header-on')) {
                headerMenu.stop().removeClass('fadeIn').addClass('fadeOut')
                setTimeout(function () {
                    body.removeClass('header-on')
                    headerMenu.removeClass('fadeOut')
                    headerMenu.addClass('fadeIn')
                }, 200)
            }
        }
    })

    const toggleMenu = () => {
        const body = document.querySelector('body')

        if (body?.classList.contains('menu-open')) {
            body?.classList.remove('menu-open')
        } else {
            body?.classList.add('menu-open')
        }
    }

    document
        .querySelector('.mobileMenuButton')
        ?.addEventListener('click', toggleMenu)
    document
        .querySelector('.mobileMenuButtonClose')
        ?.addEventListener('click', toggleMenu)

    $(document.body).on('added_to_cart', function (e) {
        console.log(e)
        console.log('EVENT: added_to_cart')
    })
})
