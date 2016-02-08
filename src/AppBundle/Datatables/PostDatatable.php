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
                        'pdf',
                    ),
                'responsive' => true
            )
        ));

        $this->ajax->set(array(
            'url' => $this->router->generate('post_results'),
            'type' => 'GET'
        ));

        $this->options->set(array(
            'class' => Style::BOOTSTRAP_3_STYLE,
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'use_integration_options' => true,
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
                'search_type' => 'eq',
            ))
            ->add('title', 'column', array(
                'title' => 'Title',
                'editable' => true
            ))
            ->add('visible', 'boolean', array(
                'title' => 'Visible',
                'true_label' => 'Yes',
                'false_label' => 'No',
            ))
            ->add('publishedAt', 'datetime', array(
                'title' => 'Published at',
                'name' => 'daterange',
            ))
            ->add('updatedAt', 'datetime', array(
                'title' => 'Updated at',
                'name' => 'daterange',
            ))
            ->add('createdby.username', 'column', array(
                'title' => 'Created By',
                'filter_type' => 'select',
                'filter_options' => array('' => 'All') + $this->getCollectionAsOptionsArray($users, 'username', 'username'),
                'filter_property' => 'createdby.username',
                'search_type' => 'eq',
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