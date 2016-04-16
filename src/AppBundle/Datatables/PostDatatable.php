<?php

namespace AppBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class PostDatatable
 *
 * @package AppBundle\Datatables
*/
class PostDatatable extends AbstractDatatableView
{
    /**
     * {@inheritdoc}
     */

    public function getLineFormatter()
    {
        $router = $this->router;

        $formatter = function($line) use ($router) {
            $route = $router->generate('profile_show', array('id' => $line['createdBy']['id']));
            $line['createdBy']['username'] = '<a href="' . $route . '">' . $line['createdBy']['username'] . '</a>';

            return $line;
        };

        return $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
        $users = $this->em->getRepository('AppBundle:User')->findAll();

        $this->topActions->set(array(
            'start_html' => '<div class="row"><div class="col-sm-3">',
            'end_html' => '<hr></div></div>',
            'actions' => array(
                array(
                    'route' => $this->router->generate('post_new'),
                    'label' => $this->translator->trans('datatables.actions.new'),
                    'icon' => 'glyphicon glyphicon-plus',
                    'attributes' => array(
                        'rel' => 'tooltip',
                        'title' => $this->translator->trans('datatables.actions.new'),
                        'class' => 'btn btn-primary',
                        'role' => 'button'
                    ),
                )
            )
        ));

        $this->features->set(array(
            'scroll_x' => false,
            'extensions' => array(
                'buttons' =>
                    array(
                        'excel',
                        'pdf' => array(
                            'extend' => 'pdf',
                            'exportOptions' => array(
                                // show only the following columns:
                                'columns' => array(
                                    '2', // title column
                                    '3', // visible column
                                    '4', // publishedAt column
                                    '5', // updatedAt column
                                    '6', // createdBy column
                                )
                            )
                        ),
                    ),
                'responsive' => true
            )
        ));

        $this->ajax->set(array(
            'url' => $this->router->generate('post_results'),
            'type' => 'GET'
        ));

        $this->options->set(array(
            'length_menu' => array(10, 25, 50, 100, -1),
            'class' => Style::BOOTSTRAP_3_STYLE,
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'use_integration_options' => true,
        ));

        $this->callbacks->set(array(
            'init_complete' => ':post:init.js.twig'
        ));

        $this->events->set(array(
            'order' => ':post:order.js.twig'
        ));

        $this->columnBuilder
            ->add(null, 'multiselect', array(
                'start_html' => '<div class="wrapper" id="testwrapper">',
                'end_html' => '</div>',
                'attributes' => array(
                    'class' => 'testclass',
                    'name' => 'testname',
                ),
                'actions' => array(
                    array(
                        'route' => 'post_bulk_delete',
                        'label' => 'Delete',
                        'icon' => 'fa fa-times',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Delete',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                    )
                )
            ))
            ->add('id', 'column', array(
                'title' => 'Id',
                'filter' => array('text', array(
                    'search_type' => 'eq'
                ))
            ))
            ->add('title', 'column', array(
                'title' => 'Title',
                'editable' => true
            ))
            ->add('visible', 'boolean', array(
                'title' => 'Visible',
                'true_label' => 'Yes',
                'false_label' => 'No',
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => 'All', '1' => 'Yes', '0' => 'No')
                ))
            ))
            ->add('publishedAt', 'datetime', array(
                'title' => 'Published at',
                'filter' => array('daterange', array())
            ))
            ->add('updatedAt', 'datetime', array(
                'title' => 'Updated at',
                'filter' => array('daterange', array())
            ))
            ->add('createdBy.username', 'column', array(
                'title' => 'Created By',
                'filter' => array('select', array(
                    'search_type' => 'eq',
                    'select_options' => array('' => 'All') + $this->getCollectionAsOptionsArray($users, 'username', 'username'),
                ))
            ))
            ->add(null, 'action', array(
                'title' => $this->translator->trans('datatables.actions.title'),
                'actions' => array(
                    array(
                        'route' => 'post_show',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => $this->translator->trans('datatables.actions.show'),
                        'icon' => 'glyphicon glyphicon-eye-open',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.show'),
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                    ),
                    array(
                        'route' => 'post_edit',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => $this->translator->trans('datatables.actions.edit'),
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.edit'),
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                        'role' => 'ROLE_ADMIN',
                        'render_if' => function($rowEntity) {
                            return ($rowEntity['visible'] === true);
                        },
                    )
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'AppBundle\Entity\Post';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'post_datatable';
    }
}
