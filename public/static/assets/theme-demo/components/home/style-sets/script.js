$(".style-set-slide").slick({
    dots: false,
    infinite: false,
    speed: 500,
    arrows: false,
    slidesToShow: 3,
    slidesToScroll: 2,
    prevArrow: '<span class="btn-prev"><i class="fa fa-angle-left"></i></span>',
    nextArrow: '<span class="btn-next"><i class="fa fa-angle-right"></i></span>',
    responsive: [
      {
        breakpoint: 1366,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 1100,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 420,
        settings: {
          slidesToShow: 1,
        },
      },
    ],
  });
  // doan